<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ClientTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create role and user
        Role::create(['name' => 'Staff']);
        $this->user = User::factory()->create([
            'company_id' => 1,
        ]);
        $this->user->assignRole('Staff');
        $this->user->givePermissionTo('manage-clients');
    }

    public function test_staff_can_view_clients_index(): void
    {
        Client::factory()->create(['company_id' => 1, 'name' => 'Test Client']);

        $response = $this->actingAs($this->user)->get(route('clients.index'));

        $response->assertStatus(200);
        $response->assertSee('Test Client');
    }

    public function test_staff_can_create_client(): void
    {
        $response = $this->actingAs($this->user)->post(route('clients.store'), [
            'name' => 'New Client',
            'email' => 'client@example.com',
            'phone' => '1234567890',
            'address' => '123 Main St',
        ]);

        $response->assertRedirect(route('clients.show', Client::where('name', 'New Client')->first()));
        $this->assertDatabaseHas('clients', [
            'name' => 'New Client',
            'email' => 'client@example.com',
            'company_id' => 1,
        ]);
    }

    public function test_staff_can_update_client(): void
    {
        $client = Client::factory()->create(['company_id' => 1]);

        $response = $this->actingAs($this->user)->patch(route('clients.update', $client), [
            'name' => 'Updated Client',
            'email' => 'updated@example.com',
        ]);

        $response->assertRedirect(route('clients.show', $client));
        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'name' => 'Updated Client',
        ]);
    }

    public function test_staff_cannot_delete_client_with_invoices(): void
    {
        $client = Client::factory()->create(['company_id' => 1]);
        \App\Models\Invoice::factory()->create(['client_id' => $client->id, 'company_id' => 1]);

        $response = $this->actingAs($this->user)->delete(route('clients.destroy', $client));

        $response->assertRedirect(route('clients.show', $client));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('clients', ['id' => $client->id]);
    }

    public function test_agent_cannot_create_client(): void
    {
        $agent = User::factory()->create(['company_id' => 1]);
        $agent->assignRole('Agent');

        $response = $this->actingAs($agent)->post(route('clients.store'), [
            'name' => 'New Client',
        ]);

        $response->assertForbidden();
    }
}
