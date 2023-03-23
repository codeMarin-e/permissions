<?php

namespace App\Models;

use \Spatie\Permission\Models\Permission as PermissionBase;
use App\Traits\MacroableModel;
use App\Traits\AddVariable;

class Permission extends PermissionBase {
    use MacroableModel;
    use AddVariable;

    // @HOOK_TRAITS
}
