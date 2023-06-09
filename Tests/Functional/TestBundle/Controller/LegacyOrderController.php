<?php

namespace JMS\Payment\CoreBundle\Tests\Functional\TestBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use JMS\Payment\CoreBundle\Form\ChoosePaymentMethodType;
use JMS\Payment\CoreBundle\Tests\Functional\TestBundle\Entity\Order;
use JMS\Payment\CoreBundle\Util\Legacy;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Response;

class LegacyOrderController extends AbstractController
{
    public function paymentDetailsAction($orderId, Request $request)
    {
        $order = $this->getDoctrine()->getManager()->getRepository(Order::class)->find($orderId);

        $formType = Legacy::supportsFormTypeClass()
            ? ChoosePaymentMethodType::class
            : 'jms_choose_payment_method'
        ;

        /** @var FormFactory $formFactory */
        $formFactory = $this->get('form.factory');

        $form = $formFactory->create($formType, null, ['currency' => 'EUR', 'amount' => $order->getAmount(), 'predefined_data' => ['test_plugin' => ['foo' => 'bar']]]);

        $em = $this->getDoctrine()->getManager();
        $ppc = $this->get('payment.plugin_controller');

        $request = Legacy::supportsRequestService()
            ? $request
            : $this->get('request_stack')->getCurrentRequest()
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $instruction = $form->getData();
            $ppc->createPaymentInstruction($instruction);

            $order->setPaymentInstruction($instruction);
            $em->persist($order);
            $em->flush();

            return new Response('', 201);
        }

        return $this->render('@TestBundle/Order/paymentDetails.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
