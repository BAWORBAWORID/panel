<?php

namespace Pterodactyl\Http\Controllers\Api\Application\Servers;

use Pterodactyl\Models\Server;
use Pterodactyl\Exceptions\Http\HttpForbiddenException;
use Pterodactyl\Services\Servers\BuildModificationService;
use Pterodactyl\Services\Servers\DetailsModificationService;
use Pterodactyl\Transformers\Api\Application\ServerTransformer;
use Pterodactyl\Http\Controllers\Api\Application\ApplicationApiController;
use Pterodactyl\Http\Requests\Api\Application\Servers\UpdateServerDetailsRequest;
use Pterodactyl\Http\Requests\Api\Application\Servers\UpdateServerBuildConfigurationRequest;

class ServerDetailsController extends ApplicationApiController
{
    public function __construct(
        private BuildModificationService $buildModificationService,
        private DetailsModificationService $detailsModificationService,
    ) {
        parent::__construct();
    }

    /**
     * Update the details for a specific server.
     *
     * Blocked for non-superadmin (id != 1) if the server has hide_from_admin enabled.
     * This prevents admins from changing owner_id to bypass privacy mode.
     *
     * @throws HttpForbiddenException
     * @throws \Pterodactyl\Exceptions\DisplayException
     * @throws \Pterodactyl\Exceptions\Model\DataValidationException
     * @throws \Pterodactyl\Exceptions\Repository\RecordNotFoundException
     */
    public function details(UpdateServerDetailsRequest $request, Server $server): array
    {
        $this->checkPrivacyAccess($request, $server);

        $updated = $this->detailsModificationService->returnUpdatedModel()->handle(
            $server,
            $request->validated()
        );

        return $this->fractal->item($updated)
            ->transformWith($this->getTransformer(ServerTransformer::class))
            ->toArray();
    }

    /**
     * Update the build details for a specific server.
     *
     * @throws HttpForbiddenException
     * @throws \Pterodactyl\Exceptions\DisplayException
     * @throws \Pterodactyl\Exceptions\Model\DataValidationException
     * @throws \Pterodactyl\Exceptions\Repository\RecordNotFoundException
     */
    public function build(UpdateServerBuildConfigurationRequest $request, Server $server): array
    {
        $this->checkPrivacyAccess($request, $server);

        $server = $this->buildModificationService->handle($server, $request->validated());

        return $this->fractal->item($server)
            ->transformWith($this->getTransformer(ServerTransformer::class))
            ->toArray();
    }

    /**
     * Throw 403 if a non-superadmin tries to modify a privacy-protected server.
     *
     * @throws HttpForbiddenException
     */
    private function checkPrivacyAccess(UpdateServerDetailsRequest|UpdateServerBuildConfigurationRequest $request, Server $server): void
    {
        if ($server->hide_from_admin && $request->user()->id !== 1) {
            throw new HttpForbiddenException(
                'This server has privacy mode enabled. Only the primary administrator can modify it.'
            );
        }
    }
}
