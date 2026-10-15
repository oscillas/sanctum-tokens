<?php

namespace Jeffbeltran\SanctumTokens\Http;

use Illuminate\Http\Request;
use Laravel\Nova\Nova;

class SanctumController
{
    public function tokens(Request $request, $resourceName, $id)
    {
        $user = $this->authorizedUser($request, $resourceName, $id);

        return $user->load('tokens');
    }

    public function createToken(Request $request, $resourceName, $id)
    {
        $user = $this->authorizedUser($request, $resourceName, $id);

        $abilities = collect(explode(',', $request->abilities))
            ->map(function ($item) {
                return trim($item);
            })
            ->toArray();

        $token = $user->createToken($request->name, $abilities);

        return $token->plainTextToken;
    }

    public function revoke(Request $request, $resourceName, $id)
    {
        $user = $this->authorizedUser($request, $resourceName, $id);

        return $user
            ->tokens()
            ->whereKey($request->token_id)
            ->delete();
    }

    /**
     * Resolve the target model and abort unless the request user may update it.
     *
     * Token management is as sensitive as editing the underlying model, so the
     * update ability gates every route before any token is listed, created or
     * revoked.
     */
    private function authorizedUser(Request $request, $resourceName, $id)
    {
        $user = Nova::modelInstanceForKey($resourceName)->find($id);

        abort_unless($user !== null, 404);

        abort_unless(
            Nova::user($request)?->can('update', $user),
            403
        );

        return $user;
    }
}
