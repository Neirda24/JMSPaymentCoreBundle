<?php

namespace JMS\Payment\CoreBundle\Tests\Plugin;

use PHPUnit\Framework\TestCase;
use JMS\Payment\CoreBundle\Plugin\GatewayPlugin;
use JMS\Payment\CoreBundle\BrowserKit\Request;

class GatewayPluginTest extends TestCase
{
    public function testRequest()
    {
        if (!extension_loaded('curl')) {
            $this->markTestSkipped('cURL is not loaded.');
        }

        $plugin = $this->getPlugin();

        // not sure if there is a better approach to testing this
        $request = new Request('https://raw.githubusercontent.com/schmittjoh/JMSPaymentCoreBundle/master/Tests/Plugin/Fixtures/sampleResponse', 'GET');
        $response = $plugin->request($request);

        $this->assertEquals(file_get_contents(__DIR__.'/Fixtures/sampleResponse'), $response->getContent());
        $this->assertEquals(200, $response->getStatusCode());
    }

    protected function getPlugin()
    {
        return $this->getMockForAbstractClass(GatewayPlugin::class, [true]);
    }
}
