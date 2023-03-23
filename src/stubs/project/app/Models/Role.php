<?php

namespace App\Models;

use \Spatie\Permission\Models\Role as RoleBase;
use App\Traits\MacroableModel;
use App\Traits\AddVariable;

class Role extends RoleBase {
    use MacroableModel;
    use AddVariable;

    // @HOOK_TRAITS
}
