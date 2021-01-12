<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProgramControllerTest extends WebTestCase
{
   public function testRouteProgram()
   {
       $client = self::createClient();
       $client->request('GET', '/programs');

       $this->assertTrue($client->getResponse()->isSuccessful());
   }

  
}
