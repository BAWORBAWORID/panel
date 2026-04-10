<?php

namespace Pterodactyl\Http\Controllers\Api\Client;

use Pterodactyl\Models\Server;
use Pterodactyl\Models\Permission;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Pterodactyl\Models\Filters\MultiFieldServerFilter;
use Pterodactyl\Transformers\Api\Client\ServerTransformer;
use Pterodactyl\Http\Requests\Api\Client\GetServersRequest;

class ClientController extends ClientApiController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(GetServersRequest $request): array
    {
        $user = $request->user();
        $transformer = $this->getTransformer(ServerTransformer::class);

        $builder = QueryBuilder::for(
            Server::query()->with($this->getIncludesForTransformer($transformer, ['node']))
        )->allowedFilters([
            'uuid',
            'name',
            'description',
            'external_id',
            AllowedFilter::custom('*', new MultiFieldServerFilter()),
        ]);

        $type = $request->input('type');

        if (in_array($type, ['admin', 'admin-all'])) {
            if (!$user->root_admin) {
                $builder->whereRaw('1 = 2');
            } else {
                $builder = $type === 'admin-all'
                    ? $builder
                    : $builder->whereNotIn('servers.id', $user->accessibleServers()->pluck('id')->all());
                    
            }
        } elseif ($type === 'owner') {
            $builder = $builder->where('servers.owner_id', $user->id);
        } else {
            $builder = $builder->whereIn('servers.id', $user->accessibleServers()->pluck('id')->all());
        }

        $servers = $builder->paginate(min($request->query('per_page', 50), 100))->appends($request->query());

        return $this->fractal->transformWith($transformer)->collection($servers)->toArray();
    }

    public function permissions(): array
    {
        return [
            'object' => 'system_permissions',
            'attributes' => [
                'permissions' => Permission::permissions(),
            ],
        ];
    }
}
