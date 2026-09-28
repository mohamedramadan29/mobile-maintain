<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * منح صلاحية "تسليم الفواتير" للأدوار التي تدير الفواتير.
     * قبل ذلك كانت أزرار التسليم الجماعي محمية بصلاحية delete_invoice،
     * فلم تكن تظهر لموظفي الاستقبال وهم أصحاب عملية التسليم فعلياً.
     */
    public function up(): void
    {
        $this->applyToRolesWith('invoices', fn($permissions) => array_values(array_unique([...$permissions, 'deliver_invoice'])));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->applyToRolesWith('deliver_invoice', fn($permissions) => array_values(array_diff($permissions, ['deliver_invoice'])));
    }

    private function applyToRolesWith(string $needle, callable $transform): void
    {
        foreach (DB::table('roles')->get() as $role) {
            $permissions = json_decode($role->permission, true);

            if (!is_array($permissions) || !in_array($needle, $permissions, true)) {
                continue;
            }

            DB::table('roles')->where('id', $role->id)->update([
                'permission' => json_encode($transform($permissions), JSON_UNESCAPED_UNICODE),
                'updated_at' => now(),
            ]);
        }
    }
};
