<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Memastikan route utama mengarahkan pengunjung ke katalog barang (/items).
     */
    public function test_the_application_redirects_to_items_catalog(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/items');
    }
}
