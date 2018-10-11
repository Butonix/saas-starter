<?php

namespace Tests\Unit\System\Rules;

use Tests\TestCase;
use App\Rules\ValidSubdomain;

class ValidSubdomainTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->rule = new ValidSubdomain;
    }

    /** @test */
    function it_cannot_start_with_a_dash()
    {
        $this->assertFalse($this->rule->passes('subdomain', '-invalid'));
    }

    /** @test */
    function it_cannot_end_with_a_dash()
    {
        $this->assertFalse($this->rule->passes('subdomain', 'invalid-'));
    }

    /** @test */
    function it_cannot_contain_special_characters()
    {
        $this->assertFalse($this->rule->passes('subdomain', 'this*is?invalid!'));
    }

    /** @test */
    function it_cannot_be_a_reserved_word()
    {
        $this->assertFalse($this->rule->passes('subdomain', 'www'));
    }

    /** @test */
    function it_can_contain_alpha_numeric_characters()
    {
        $this->assertTrue($this->rule->passes('subdomain', 'valid'));
    }

    /** @test */
    function it_can_contain_dashes()
    {
        $this->assertTrue($this->rule->passes('subdomain', 'valid-subdomain'));
    }
}
