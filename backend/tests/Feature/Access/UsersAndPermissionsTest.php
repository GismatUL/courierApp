<?php

namespace Tests\Feature\Access;

use App\Modules\Access\Database\Seeders\StaffSeeder;
use App\Modules\Access\Livewire\Designations;
use App\Modules\Access\Livewire\Profile;
use App\Modules\Access\Livewire\RoleEditor;
use App\Modules\Access\Livewire\Roles;
use App\Modules\Access\Livewire\UserPermissions;
use App\Modules\Access\Livewire\Users;
use App\Modules\Access\Models\Role;
use App\Modules\Access\Models\User;
use App\Modules\Network\Livewire\Hubs;
use App\Modules\Network\Models\Hub;
use App\Shared\Support\Permissions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Livewire;
use Tests\TestCase;

class UsersAndPermissionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(StaffSeeder::class);
    }

    private function operatorRole(array $permissions = ['hub_read']): Role
    {
        return tap(Role::where('slug', 'operator')->firstOrFail())->update(['permissions' => $permissions]);
    }

    public function test_permission_catalogue_keeps_spec_keys_and_is_unique(): void
    {
        $all = Permissions::all();

        $this->assertSame($all, array_values(array_unique($all)));
        foreach (['dashboard_read', 'hub_read', 'invoice_generate_menually', 'parcel_total_summery', 'shelf_assing', 'total_parcels_deliverd', 'permission_update'] as $key) {
            $this->assertContains($key, $all);
        }
        $this->assertSame(['hub_read'], Permissions::sanitize(['hub_read', 'made_up', 'hub_read']));
    }

    public function test_user_without_permission_gets_403_and_menu_hides_the_item(): void
    {
        $this->actingAs(User::factory()->withPermissions(['zone'])->create());

        $this->get('/admin/hubs')->assertForbidden();
        $this->get('/admin/zones')->assertOk();
        $this->get('/admin')->assertOk()
            ->assertDontSee('href="'.route('admin.hubs.index').'"', false)
            ->assertSee('href="'.route('admin.zones.index').'"', false)
            ->assertDontSee('İstifadəçilər və Rollar');
    }

    public function test_read_only_user_cannot_create_even_by_calling_the_action(): void
    {
        $this->actingAs(User::factory()->withPermissions(['hub_read'])->create());

        Livewire::test(Hubs::class)->assertDontSee('Əlavə et')->call('create')->assertForbidden();

        $hub = Hub::create(['name' => 'Bakı', 'address' => 'x', 'latitude' => 40, 'longitude' => 49]);
        Livewire::test(Hubs::class)->call('confirmDelete', $hub->id)->assertForbidden();
        $this->assertModelExists($hub);
    }

    public function test_new_user_gets_the_role_permissions_and_hashed_password(): void
    {
        $this->actingAs(User::factory()->superAdmin()->create());
        $role = $this->operatorRole(['hub_read', 'zone']);
        $hub = Hub::create(['name' => 'Bakı', 'address' => 'x', 'latitude' => 40, 'longitude' => 49]);

        Livewire::test(Users::class)
            ->call('create')
            ->set('form.name', 'Operator Bir')->set('form.email', 'op1@kuryer.test')
            ->set('form.password', 'operator-pass-1')->set('form.role_id', (string) $role->id)
            ->set('form.hub_ids', [(string) $hub->id])
            ->call('save')
            ->assertHasNoErrors();

        $user = User::where('email', 'op1@kuryer.test')->firstOrFail();
        $this->assertSame(['hub_read', 'zone'], $user->permissions);
        $this->assertTrue(Hash::check('operator-pass-1', $user->password));
        $this->assertSame([$hub->id], $user->hubs->pluck('id')->all());
    }

    public function test_password_rules_and_unique_email(): void
    {
        $this->actingAs($admin = User::factory()->superAdmin()->create());
        $role = $this->operatorRole();

        Livewire::test(Users::class)
            ->call('create')
            ->set('form.name', 'X')->set('form.email', $admin->email)->set('form.role_id', (string) $role->id)
            ->set('form.password', 'short')
            ->call('save')
            ->assertHasErrors(['form.email' => 'unique', 'form.password' => 'min'])
            ->set('form.password', str_repeat('a', 73))
            ->call('save')
            ->assertHasErrors(['form.password' => 'max']);
    }

    public function test_editing_without_password_keeps_it_and_role_change_resets_permissions(): void
    {
        $this->actingAs(User::factory()->superAdmin()->create());
        $role = $this->operatorRole(['hub_read']);
        $branch = tap(Role::where('slug', 'branch')->firstOrFail())->update(['permissions' => ['zone']]);
        $user = User::factory()->create(['role_id' => $role->id, 'permissions' => ['hub_read', 'hub_create'], 'password' => 'original-pass-1']);

        Livewire::test(Users::class)->call('edit', $user->id)->set('form.name', 'Yeni Ad')->call('save')->assertHasNoErrors();
        $user->refresh();
        $this->assertTrue(Hash::check('original-pass-1', $user->password));
        $this->assertSame(['hub_read', 'hub_create'], $user->permissions, 'Same role keeps the custom set');

        Livewire::test(Users::class)->call('edit', $user->id)->set('form.role_id', (string) $branch->id)->call('save');
        $this->assertSame(['zone'], $user->fresh()->permissions);
    }

    public function test_only_super_admin_can_grant_or_touch_super_admins(): void
    {
        $super = User::factory()->superAdmin()->create();
        $manager = User::factory()->withPermissions(['user_read', 'user_create', 'user_update', 'user_delete'])->create();
        $this->actingAs($manager);
        $superRole = Role::where('slug', Role::SUPER_ADMIN)->firstOrFail();

        Livewire::test(Users::class)
            ->call('create')
            ->set('form.name', 'Hacker')->set('form.email', 'h@kuryer.test')->set('form.password', 'password-1234')
            ->set('form.role_id', (string) $superRole->id)
            ->call('save')
            ->assertHasErrors('form.role_id');

        Livewire::test(Users::class)->call('edit', $super->id)->assertHasErrors('form.name');
        Livewire::test(Users::class)->call('confirmDelete', $super->id)->call('delete')->assertDispatched('toast', type: 'error');
        $this->assertModelExists($super);
    }

    public function test_form_id_is_locked_against_tampering(): void
    {
        $this->actingAs(User::factory()->superAdmin()->create());

        $this->expectException(CannotUpdateLockedPropertyException::class);
        Livewire::test(Users::class)->call('create')->set('form.id', 1);
    }

    public function test_user_cannot_delete_self_or_change_own_role(): void
    {
        $this->actingAs($admin = User::factory()->superAdmin()->create());
        $operator = $this->operatorRole();

        Livewire::test(Users::class)->call('confirmDelete', $admin->id)->call('delete')->assertDispatched('toast', type: 'error');
        Livewire::test(Users::class)->call('edit', $admin->id)->set('form.role_id', (string) $operator->id)->call('save')->assertHasErrors('form.role_id');
        Livewire::test(Users::class)->call('edit', $admin->id)->set('form.status', false)->call('save')->assertHasErrors('form.role_id');

        $this->assertModelExists($admin);
        $this->assertTrue($admin->fresh()->isSuperAdmin());
    }

    public function test_last_super_admin_cannot_be_deleted(): void
    {
        $other = User::factory()->superAdmin()->create();
        $this->actingAs(User::factory()->superAdmin()->create());

        Livewire::test(Users::class)->call('confirmDelete', $other->id)->call('delete')->assertDispatched('toast', type: 'success');
        $this->assertModelMissing($other);
    }

    public function test_role_editor_creates_role_with_slug_and_sanitised_permissions(): void
    {
        $this->actingAs(User::factory()->superAdmin()->create());

        Livewire::test(RoleEditor::class)
            ->set('name', 'Anbar Müdiri')
            ->set('permissions', ['hub_read', 'not_a_key'])
            ->call('toggleModule', 'zone')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.roles.index'));

        $role = Role::where('name', 'Anbar Müdiri')->firstOrFail();
        $this->assertSame('anbar-mudiri', $role->slug);
        $this->assertContains('hub_read', $role->permissions);
        $this->assertContains('region_delete', $role->permissions);
        $this->assertNotContains('not_a_key', $role->permissions);
    }

    public function test_non_super_admin_cannot_grant_permissions_they_do_not_hold(): void
    {
        $this->actingAs(User::factory()->withPermissions(['role_read', 'role_create', 'hub_read'])->create());

        Livewire::test(RoleEditor::class)
            ->set('name', 'Genişləndirilmiş')
            ->set('permissions', ['hub_read', 'user_delete'])
            ->call('save')
            ->assertHasErrors('permissions');

        $this->assertDatabaseMissing('roles', ['name' => 'Genişləndirilmiş']);
    }

    public function test_super_admin_and_used_roles_cannot_be_deleted(): void
    {
        $this->actingAs(User::factory()->superAdmin()->create());
        $super = Role::where('slug', Role::SUPER_ADMIN)->firstOrFail();
        $operator = $this->operatorRole();
        User::factory()->create(['role_id' => $operator->id]);

        Livewire::test(Roles::class)->call('confirmDelete', $super->id)->call('delete')->assertDispatched('toast', type: 'error');
        Livewire::test(Roles::class)->call('confirmDelete', $operator->id)->call('delete')->assertDispatched('toast', type: 'error');

        $this->assertModelExists($super);
        $this->assertModelExists($operator);
    }

    public function test_individual_permissions_page_saves_and_resets_to_role(): void
    {
        $this->actingAs(User::factory()->superAdmin()->create());
        $role = $this->operatorRole(['hub_read']);
        $user = User::factory()->create(['role_id' => $role->id, 'permissions' => ['hub_read']]);

        Livewire::test(UserPermissions::class, ['user' => $user])
            ->set('permissions', ['hub_read', 'hub_create'])
            ->call('save')
            ->assertHasNoErrors();
        $this->assertSame(['hub_read', 'hub_create'], $user->fresh()->permissions);

        Livewire::test(UserPermissions::class, ['user' => $user])->call('resetToRole')->assertSet('permissions', ['hub_read']);
    }

    public function test_own_and_super_admin_permissions_pages_are_forbidden(): void
    {
        $this->actingAs($admin = User::factory()->superAdmin()->create());

        $this->get(route('admin.users.permissions', $admin))->assertForbidden();
        $this->get(route('admin.users.permissions', User::factory()->superAdmin()->create()))->assertForbidden();
    }

    public function test_designations_crud_uses_its_own_permissions(): void
    {
        $this->actingAs(User::factory()->withPermissions(['designation_read', 'designation_create'])->create());

        Livewire::test(Designations::class)->call('create')->set('form.title', 'Dispetçer')->call('save')->assertHasNoErrors();
        $this->assertDatabaseHas('designations', ['title' => 'Dispetçer']);

        Livewire::test(Designations::class)->call('create')->set('form.title', 'Operator')->call('save')->assertHasErrors(['form.title' => 'unique']);
        Livewire::test(Designations::class)->call('edit', 1)->assertForbidden();
    }

    public function test_password_change_requires_the_old_password(): void
    {
        $this->actingAs($user = User::factory()->create(['password' => 'old-password-1']));

        Livewire::test(Profile::class)
            ->set('old_password', 'wrong')->set('new_password', 'new-password-1')->set('new_password_confirmation', 'new-password-1')
            ->call('updatePassword')
            ->assertHasErrors(['old_password']);

        Livewire::test(Profile::class)
            ->set('old_password', 'old-password-1')->set('new_password', 'new-password-1')->set('new_password_confirmation', 'mismatch')
            ->call('updatePassword')
            ->assertHasErrors(['new_password' => 'confirmed']);

        Livewire::test(Profile::class)
            ->set('old_password', 'old-password-1')->set('new_password', 'new-password-1')->set('new_password_confirmation', 'new-password-1')
            ->call('updatePassword')
            ->assertHasNoErrors();

        $this->assertTrue(Hash::check('new-password-1', $user->fresh()->password));
    }

    public function test_staff_seeder_is_idempotent(): void
    {
        $this->seed(StaffSeeder::class);

        $this->assertSame(5, Role::count());
        $this->assertDatabaseHas('roles', ['slug' => Role::SUPER_ADMIN]);
    }
}
