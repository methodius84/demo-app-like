<?php

namespace App\Services\Bitrix;

use App\Models\Organization;
use Illuminate\Http\Request;

interface ServiceInterface
{
    public function setOrganization(Organization $organization) : self;
    public function getOrganization() : Organization;
    public function get(Request $request);

    public function create(Request $request);

    public function update(Request $request);

    public function delete(Request $request);

    public function sync();
}
