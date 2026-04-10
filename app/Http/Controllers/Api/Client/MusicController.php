<?php

namespace Pterodactyl\Http\Controllers\Api\Client;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Pterodactyl\Models\UserMusicTrack;
use Pterodactyl\Models\UserMusicPreference;
use Pterodactyl\Http\Controllers\Controller;

class MusicController extends Controller
{
    private const MAX_UPLOAD_MB = 20;
    private const MAX_TRACKS    = 50;
    private const YT_API_BASE   = 'https://music.obscuraworks.org/api/ytmp3';

    // ─────────────────────────────────────────────────────────────────────────
    // GET /api/client/account/music
    // ─────────────────────────────────────────────────────────────────────────
    public function index(Request $request): JsonResponse
    {
        $user   = $request->user();
        $tracks = UserMusicTrack::where('user_id', $user->id)
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->get()
                    ->map(fn($t) => $this->formatTrack($t))
                    ->values();

        // Global track selalu di posisi 0, tidak disimpan ke DB
        $globalTrack = [
            'id'               => 'global',
            'source'           => 'global',
            'title'            => 'Nadhif Basalamah - kota ini tak sama tanpamu',
            'artist'           => 'Global Mix',
            'public_url'       => '/assets/mp3/spotify2026.mp3',
            'duration_seconds' => null,
            'sort_order'       => -1,
        ];

        return response()->json([
            'tracks'      => collect([$globalTrack])->concat($tracks),
            'preferences' => [
                'volume'           => optional(UserMusicPreference::where('user_id', $user->id)->first())->volume ?? 70,
                'shuffle'          => optional(UserMusicPreference::where('user_id', $user->id)->first())->shuffle ?? false,
                'repeat'           => optional(UserMusicPreference::where('user_id', $user->id)->first())->repeat ?? false,
                'current_track_id' => optional(UserMusicPreference::where('user_id', $user->id)->first())->current_track_id ?? null,
            ],
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // POST /api/client/account/music/youtube
    //
    // Flow:
    //   1. GET ytmp3 API → dapat title + downloadUrl
    //   2. Download binary MP3 dari downloadUrl (follow redirect)
    //   3. Simpan ke storage/app/public/music/{user_id}/yt_{videoId}_{time}.mp3
    //   4. Simpan metadata ke DB, return track dengan public_url = /storage/music/...
    // ─────────────────────────────────────────────────────────────────────────
    public function addYoutube(Request $request): JsonResponse
    {
        $request->validate(['url' => 'required|url']);

        $user = $request->user();

        if (UserMusicTrack::where('user_id', $user->id)->count() >= self::MAX_TRACKS) {
            return response()->json(['error' => 'Playlist limit reached (' . self::MAX_TRACKS . ' tracks).'], 422);
        }

        // Step 1: Metadata dari proxy API
        $metaResponse = Http::timeout(15)->get(self::YT_API_BASE, ['url' => $request->input('url')]);

        if (!$metaResponse->successful()) {
            return response()->json(['error' => 'Failed to contact music API. Try again later.'], 502);
        }

        $meta = $metaResponse->json();

        if (empty($meta['status']) || !$meta['status'] || empty($meta['downloadUrl'])) {
            return response()->json(['error' => 'Could not process the YouTube URL.'], 422);
        }

        // Step 2: Download MP3 binary (mengikuti redirect dengan curl via Http facade)
        $downloadResponse = Http::timeout(120)
            ->withOptions(['allow_redirects' => true])
            ->get($meta['downloadUrl']);

        if (!$downloadResponse->successful() || strlen($downloadResponse->body()) < 1024) {
            return response()->json(['error' => 'Failed to download audio file. Please try again.'], 502);
        }

        // Step 3: Simpan ke disk 'public'
        $videoId   = $meta['videoId'] ?? uniqid();
        $filename  = 'yt_' . $videoId . '_' . time() . '.mp3';
        $filePath  = 'music/' . $user->id . '/' . $filename;

        Storage::disk('public')->put($filePath, $downloadResponse->body());

        // Step 4: Simpan metadata
        $track = UserMusicTrack::create([
            'user_id'    => $user->id,
            'source'     => 'youtube',
            'title'      => $meta['title'] ?? 'YouTube Track',
            'artist'     => null,
            'video_id'   => $videoId,
            'file_path'  => $filePath,
            'sort_order' => UserMusicTrack::where('user_id', $user->id)->max('sort_order') + 1,
        ]);

        return response()->json(['track' => $this->formatTrack($track)], 201);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // POST /api/client/account/music/upload
    // ─────────────────────────────────────────────────────────────────────────
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'file'  => 'required|file|mimes:mp3,ogg,wav,flac|max:' . (self::MAX_UPLOAD_MB * 1024),
            'title' => 'nullable|string|max:255',
        ]);

        $user = $request->user();

        if (UserMusicTrack::where('user_id', $user->id)->count() >= self::MAX_TRACKS) {
            return response()->json(['error' => 'Playlist limit reached (' . self::MAX_TRACKS . ' tracks).'], 422);
        }

        $file     = $request->file('file');
        $filename = 'up_' . uniqid('', true) . '.' . $file->getClientOriginalExtension();
        $filePath = 'music/' . $user->id . '/' . $filename;

        // Simpan ke disk 'public' — sama dengan youtube
        $file->storeAs('music/' . $user->id, $filename, 'public');

        $title = $request->input('title')
            ?: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        $track = UserMusicTrack::create([
            'user_id'    => $user->id,
            'source'     => 'upload',
            'title'      => $title,
            'file_path'  => $filePath,
            'sort_order' => UserMusicTrack::where('user_id', $user->id)->max('sort_order') + 1,
        ]);

        return response()->json(['track' => $this->formatTrack($track)], 201);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // DELETE /api/client/account/music/{track}
    // ─────────────────────────────────────────────────────────────────────────
    public function destroy(Request $request, int $trackId): JsonResponse
    {
        $user  = $request->user();
        $track = UserMusicTrack::where('id', $trackId)
                    ->where('user_id', $user->id)
                    ->firstOrFail();

        if ($track->file_path) {
            Storage::disk('public')->delete($track->file_path);
        }

        $track->delete();

        return response()->json(null, 204);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PUT /api/client/account/music/preferences
    // ─────────────────────────────────────────────────────────────────────────
    public function updatePreferences(Request $request): JsonResponse
    {
        $request->validate([
            'volume'           => 'sometimes|integer|min:0|max:100',
            'shuffle'          => 'sometimes|boolean',
            'repeat'           => 'sometimes|boolean',
            'current_track_id' => 'sometimes|nullable|integer',
        ]);

        $user  = $request->user();
        $prefs = UserMusicPreference::updateOrCreate(
            ['user_id' => $user->id],
            $request->only(['volume', 'shuffle', 'repeat', 'current_track_id'])
        );

        return response()->json([
            'preferences' => [
                'volume'           => $prefs->volume,
                'shuffle'          => $prefs->shuffle,
                'repeat'           => $prefs->repeat,
                'current_track_id' => $prefs->current_track_id,
            ],
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helper: serialize track untuk API response
    //
    // file_path disimpan relatif dari disk 'public' → 'music/{user_id}/filename.mp3'
    // public URL = /storage/music/{user_id}/filename.mp3
    // (karena `php artisan storage:link` membuat public/storage → storage/app/public)
    // ─────────────────────────────────────────────────────────────────────────
    private function formatTrack(UserMusicTrack $track): array
    {
        $publicUrl = match ($track->source) {
            'global'          => '/assets/mp3/spotify2026.mp3',
            'youtube', 'upload' => $track->file_path
                                    ? '/storage/' . $track->file_path
                                    : null,
            default           => null,
        };

        return [
            'id'               => $track->id,
            'source'           => $track->source,
            'title'            => $track->title,
            'artist'           => $track->artist,
            'video_id'         => $track->video_id,
            'public_url'       => $publicUrl,
            'duration_seconds' => $track->duration_seconds,
            'sort_order'       => $track->sort_order,
        ];
    }
}
