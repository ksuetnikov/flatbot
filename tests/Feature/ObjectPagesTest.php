<?php

namespace Tests\Feature;

use App\Models\Object\ObjectFinanceEntry;
use App\Models\Object\ObjectModel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ObjectPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_show_edit_and_finances_render_for_the_owner(): void
    {
        $user = User::factory()->create();

        $object = new ObjectModel;
        $object->name = 'Квартира';
        $object->description = 'Центр';
        $object->user_id = $user->id;
        $object->order = '1';
        $object->save();

        $object->financeEntries()->create([
            'year' => 2026,
            'month' => 5,
            'day' => 2,
            'type' => ObjectFinanceEntry::TYPE_INCOME,
            'amount' => '1500.50',
            'comment' => 'Аренда',
        ]);

        $this->actingAs($user);

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('dashboard')
                ->where('objects.0.name', 'Квартира')
                ->where('objects.0.finance_summary.total_income', 1500.5)
                ->where('objects.0.finance_summary.entries_count', 1));

        $this->get(route('object.show', $object))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('object/show')
                ->where('object.name', 'Квартира')
                ->where('object.finance_summary.balance', 1500.5)
                ->has('object.reading_status')
                ->has('object.subscribers')
                ->has('object.properties'));

        $this->get(route('object.edit', $object))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('object/edit')
                ->where('object.name', 'Квартира')
                ->has('object.subscribers')
                ->has('allSubscribers')
                ->has('allProperties'));

        $this->get(route('object.finances', $object))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('object/finances')
                ->where('object.name', 'Квартира')
                ->where('months.0.year', 2026)
                ->where('months.0.month', 5)
                ->where('months.0.incomes.0.day', 2)
                ->where('months.0.incomes.0.comment', 'Аренда')
                ->where('months.0.total_income', 1500.5)
                ->where('months.0.balance', 1500.5));
    }
}
