<?php

namespace Tests\Feature;

use App\Models\Rsvp;
use App\Models\User;
use App\Models\Wedding;
use App\Models\Wish;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RsvpAndWishTest extends TestCase
{
    use RefreshDatabase;

    public function test_attending_rsvp_is_stored_for_the_correct_wedding(): void
    {
        $wedding = Wedding::factory()->create(['slug' => 'ali-dan-fatimah']);
        $other = Wedding::factory()->create(['slug' => 'amir-aisyah']);

        $this->post(route('invitation.rsvp', $wedding->slug), [
            'rsvp_name' => 'Siti Aminah',
            'phone_number' => '0123456789',
            'status' => 'hadir',
            'total_pax' => 3,
            'form' => 'rsvp',
        ])->assertRedirect(route('invitation.show', $wedding->slug).'#sahkan-kehadiran');

        $this->assertDatabaseHas('rsvps', [
            'wedding_id' => $wedding->id,
            'guest_name' => 'Siti Aminah',
            'status' => Rsvp::HADIR,
            'total_pax' => 3,
        ]);
        $this->assertSame(0, $other->rsvps()->count());
    }

    public function test_absent_rsvp_always_stores_zero_pax(): void
    {
        $wedding = Wedding::factory()->create();

        $this->post(route('invitation.rsvp', $wedding->slug), [
            'rsvp_name' => 'Hakim',
            'phone_number' => '0139876543',
            'status' => 'tidak_hadir',
            'total_pax' => 5,
        ])->assertRedirect();

        $this->assertDatabaseHas('rsvps', [
            'wedding_id' => $wedding->id,
            'guest_name' => 'Hakim',
            'status' => Rsvp::TIDAK_HADIR,
            'total_pax' => 0,
        ]);
    }

    public function test_invalid_rsvp_values_are_rejected(): void
    {
        $wedding = Wedding::factory()->create();

        $this->post(route('invitation.rsvp', $wedding->slug), [
            'rsvp_name' => 'Siti',
            'phone_number' => '0123456789',
            'status' => 'hadir',
            'total_pax' => 9,
        ])->assertSessionHasErrors('total_pax');

        $this->post(route('invitation.rsvp', $wedding->slug), [
            'rsvp_name' => 'Siti',
            'phone_number' => '0123456789',
            'status' => 'mungkin',
            'total_pax' => 1,
        ])->assertSessionHasErrors('status');

        $this->post(route('invitation.rsvp', $wedding->slug), [
            'phone_number' => '0123456789',
            'status' => 'hadir',
            'total_pax' => 1,
        ])->assertSessionHasErrors('rsvp_name');

        $this->assertSame(0, $wedding->rsvps()->count());
    }

    public function test_inactive_wedding_rejects_rsvp(): void
    {
        $wedding = Wedding::factory()->inactive()->create();

        $this->post(route('invitation.rsvp', $wedding->slug), [
            'rsvp_name' => 'Siti',
            'phone_number' => '0123456789',
            'status' => 'hadir',
            'total_pax' => 1,
        ])->assertNotFound();

        $this->assertDatabaseCount('rsvps', 0);
    }

    public function test_valid_wish_is_stored_and_isolated(): void
    {
        $wedding = Wedding::factory()->create(['slug' => 'ali-dan-fatimah']);
        $other = Wedding::factory()->create(['slug' => 'amir-aisyah']);
        $other->wishes()->create([
            'guest_name' => 'Orang Lain',
            'message' => 'Ucapan majlis lain yang tidak patut muncul.',
        ]);

        $this->post(route('invitation.wish', $wedding->slug), [
            'wish_name' => 'Nur Aina',
            'message' => 'Semoga berbahagia hingga ke jannah.',
            'form' => 'wish',
        ])->assertRedirect(route('invitation.show', $wedding->slug).'#buku-ucapan');

        $this->assertDatabaseHas('wishes', [
            'wedding_id' => $wedding->id,
            'guest_name' => 'Nur Aina',
        ]);

        $this->get(route('invitation.show', $wedding->slug))
            ->assertSee('Nur Aina')
            ->assertSee('Semoga berbahagia hingga ke jannah.')
            ->assertDontSee('Orang Lain');
    }

    public function test_invalid_wish_is_rejected(): void
    {
        $wedding = Wedding::factory()->create();

        $this->post(route('invitation.wish', $wedding->slug), [
            'wish_name' => '',
            'message' => '',
        ])->assertSessionHasErrors(['wish_name', 'message']);

        $this->assertDatabaseCount('wishes', 0);
    }

    public function test_guest_content_is_escaped_and_not_executed(): void
    {
        $wedding = Wedding::factory()->create();

        $this->post(route('invitation.wish', $wedding->slug), [
            'wish_name' => 'Aina',
            'message' => 'Salam & selamat <script>alert(1)</script> "bahagia"',
        ])->assertRedirect();

        $this->get(route('invitation.show', $wedding->slug))
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('Salam &amp; selamat', false)
            ->assertSee('&quot;bahagia&quot;', false);
    }

    public function test_admin_can_delete_a_wish_but_not_one_from_another_wedding(): void
    {
        $admin = User::factory()->create();
        $wedding = Wedding::factory()->create();
        $wish = Wish::factory()->create([
            'wedding_id' => $wedding->id,
            'message' => 'Ucapan tidak sesuai.',
        ]);
        $foreign = Wish::factory()->create();

        $this->actingAs($admin)
            ->delete(route('admin.weddings.wishes.destroy', [$wedding, $foreign]))
            ->assertNotFound();

        $this->actingAs($admin)
            ->delete(route('admin.weddings.wishes.destroy', [$wedding, $wish]))
            ->assertRedirect();

        $this->assertDatabaseMissing('wishes', ['id' => $wish->id]);
        $this->assertDatabaseHas('wishes', ['id' => $foreign->id]);
    }

    public function test_admin_can_export_only_the_selected_wedding_rsvps(): void
    {
        $admin = User::factory()->create();
        $wedding = Wedding::factory()->create(['slug' => 'ali-dan-fatimah']);
        $other = Wedding::factory()->create();
        $wedding->rsvps()->create([
            'guest_name' => 'Siti Aminah',
            'phone_number' => '0123456789',
            'status' => Rsvp::HADIR,
            'total_pax' => 2,
        ]);
        $other->rsvps()->create([
            'guest_name' => 'Tetamu Lain',
            'phone_number' => '0190000000',
            'status' => Rsvp::HADIR,
            'total_pax' => 1,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.weddings.rsvps.export', $wedding));

        $response->assertOk();
        $response->assertDownload('rsvp-ali-dan-fatimah.csv');
        $content = $response->streamedContent();
        $this->assertStringContainsString('Siti Aminah', $content);
        $this->assertStringContainsString('Hadir', $content);
        $this->assertStringNotContainsString('Tetamu Lain', $content);
    }
}
