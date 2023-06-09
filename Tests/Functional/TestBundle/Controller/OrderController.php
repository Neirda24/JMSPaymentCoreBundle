<?php

namespace JMS\Payment\CoreBundle\Tests\Functional\TestBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use JMS\Payment\CoreBundle\Form\ChoosePaymentMethodType;
use JMS\Payment\CoreBundle\PluginController\PluginController;
use JMS\Payment\CoreBundle\Tests\Functional\TestBundle\Entity\Order;
use JMS\Payment\CoreBundle\Util\Legacy;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/order")
 *
 * @author Johannes
 */
class OrderController extends AbstractController
{
    /**
     * @Route("/{orderId}/payment-details", name = "payment_details")
     *
     * @param int $orderId
     * @return array|Response
     */
    public function paymentDetailsAction($orderId, PluginController $pluginController, Request $request)
    {
        $order = $this->getDoctrine()->getManager()->getRepository(Order::class)->find($orderId);

        $formType = Legacy::supportsFormTypeClass()
            ? ChoosePaymentMethodType::class
            : 'jms_choose_payment_method'
        ;

        $form = $this->get('form.factory')->create($formType, null, ['currency' => 'EUR', 'amount' => $order->getAmount(), 'predefined_data' => ['test_plugin' => ['foo' => 'bar']]]);

        $em = $this->getDoctrine()->getManager();

        $request = Legacy::supportsRequestService()
            ? $request
            : $this->get('request_stack')->getCurrentRequest()
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $instruction = $form->getData();
            $pluginController->createPaymentInstruction($instruction);

            $order->setPaymentInstruction($instruction);
            $em->persist($order);
            $em->flush();

            return new Response('', 201);
        }

        return $this->render('@TestBundle/Order/paymentDetails.html.twig', ['form' => $form->createView()]);
    }
}
