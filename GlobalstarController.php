<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use \Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Mime\Email;


class GlobalstarController extends AbstractController
{
    /**
     * We create the webhook name and URL that will be listening the post data
     * @Route("/globalstar", name="globalstar_hook", defaults={"_format"="xml"}, methods="POST")
     * @throws \Exception
     */
    public function processXML(Request $request)
    {
        /**
         * We get the params sent from the Device in $postData
         */
        $postData = file_get_contents('php://input');
        /**
         * Initialize some vars
         */
        $state = 'fail';
        $stateMessage = 'Empty Request';
        $count = 0;
        $timeStamp = '';
        $messageID = 0;
        $date = new \DateTime();
        $time = abs($date->getTimestamp());
        /**
         * The $loginUrl is the Thingsboard authentication URL, we connect to it with the Symfony HttpClient component
         * and the credentials set on Thingsboard
         */
        $loginUrl = 'http://127.0.0.1:8080/api/auth/login';
        $client = HttpClient::create();
        $login = $client->request('POST', $loginUrl, [
            'json' => ['username' => '',
                'password' => '',],
        ]);
        $decodedPayload = $login->toArray();
        /**
         * Retrieve the token from the authentication
         */
        $token = $decodedPayload['token'];

        /**
         * If data has been sent to our webhook we read them using simplexml_load_string function from PHP
         */

        if ($postData) {
            $xml = simplexml_load_string($postData);
            $state = 'pass';
            /**
             * Check that the xml has a messageId
             */
            if ($xml && isset($xml->attributes()->{'messageID'})) {
                $messageID = $xml->attributes()->{'messageID'};
                $timeStamp = $xml->attributes()->{'timeStamp'};
                if (!isset($xml->stuMessage)) {
                    $stateMessage = 'No Messages';
                } else {
                    /**
                     * If the xml has messages we loop the data
                     */
                    $em = $this->getDoctrine()->getManager();
                    foreach ($xml->stuMessage as $message) {
                        /**
                         * Check if we have already a device stored in thingsboard
                         */
                        $deviceRepo = $em->getRepository('App:Device')->findOneBy(
                            ['name' => $message->esn]
                        );
                        if ($deviceRepo) {
                            /**
                             * If we do have an existant device we retrieve the credentials from Thingsboard API
                             */
                            $data = [
                                'id' => $deviceRepo->getId(),
                                'name' => $deviceRepo->getName(),
                            ];

                            $client = HttpClient::create();
                            $response = $client->request('GET', 'http://127.0.0.1:8080/api/device/' . ($data['id']) . '/credentials',
                                [
                                    'headers' => [
                                        'X-Authorization' => 'Bearer ' . $token,
                                    ],
                                ]);
                            $payload = $response->toArray();
                            $accessToken = $payload['credentialsId'];
                            $data = ['esn' => (string)$message->esn,
                                'gps' => (string)$message->gps,
                                'unixTime' => (string)$message->unixTime,
                                'payload' => (string)$message->payload,
                                'payload_length' => (string)$message->payload->attributes()->{'length'},
                                'payload_source' => (string)$message->payload->attributes()->{'source'},
                                'payload_encoding' => (string)$message->payload->attributes()->{'encoding'},
                            ];
                            $client = HttpClient::create();
                            $postTelemetry = $client->request('POST', 'http://127.0.0.1:8080/api/v1/' . ($accessToken) . '/telemetry', [
                                'headers' => [
                                    'Accept' => 'application/json',
                                    'Content-Type' => 'application/json'
                                ],
                                'body' => json_encode($data),
                            ]);
                            if ($postTelemetry->getStatusCode() === 200) {
                                $count = $count + 1;
                            }

                        } else {
                            /**
                             * If we do not have the device on thingsboard yet, we create a new device with Thingsboard
                             * API
                             */
                            $data = [
                                "additionalInfo" => "Created Dynamically",
                                "createdTime" => time(),
                                "label" => (string)$message->esn,
                                'name' => (string)$message->esn,
                                'type' => 'Globalstar Simplex',
                            ];

                            $client = HttpClient::create();
                            $newDevice = $client->request('POST', 'http://127.0.0.1:8080/api/device', [
                                'headers' => [
                                    'Accept' => 'application/json',
                                    'Content-Type' => 'application/json',
                                    'X-Authorization' => 'Bearer ' . $token,
                                ],
                                'body' => json_encode($data),
                            ]);

                            if($newDevice->getStatusCode() === 200){
                                $dataMsg = ['esn' => (string)$message->esn,
                                    'gps' => (string)$message->gps,
                                    'unixTime' => (string)$message->unixTime,
                                    'payload' => (string)$message->payload,
                                    'payload_length' => (string)$message->payload->attributes()->{'length'},
                                    'payload_source' => (string)$message->payload->attributes()->{'source'},
                                    'payload_encoding' => (string)$message->payload->attributes()->{'encoding'},
                                ];
                                $deviceRepo = $em->getRepository('App:Device')->findOneBy(
                                    ['name' => $message->esn]
                                );
                                if ($deviceRepo) {
                                    $data = [
                                        'id' => $deviceRepo->getId(),
                                        'name' => $deviceRepo->getName(),
                                    ];

                                    $client = HttpClient::create();
                                    $response = $client->request('GET', 'http://127.0.0.1:8080/api/device/' . ($data['id']) . '/credentials',
                                        [
                                            'headers' => [
                                                'X-Authorization' => 'Bearer ' . $token,
                                            ],
                                        ]);
                                    $payload = $response->toArray();
                                    $accessToken = $payload['credentialsId'];
                                    $client = HttpClient::create();
                                    $postTelemetry = $client->request('POST', 'http://127.0.0.1:8080/api/v1/' . ($accessToken) . '/telemetry', [
                                        'headers' => [
                                            'Accept' => 'application/json',
                                            'Content-Type' => 'application/json'
                                        ],
                                        'body' => json_encode($dataMsg),
                                    ]);
                                    if ($postTelemetry->getStatusCode() === 200) {
                                        $count = $count + 1;
                                    }
                                }
                            }

                        }

                    }
                    $stateMessage = $count . ' Message(s) Processed';
                }
            }
        }
        /**
         * We return the response in XML format with information about our processing.
         */
        $response = '<?xml version="1.0" encoding="UTF-8"?>
<stuResponseMsg xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="http://cody.glpconnect.com/XSD/StuResponse_Rev1_0.xsd" deliveryTimeStamp="' . $timeStamp . '" messageID="' . $time . '" correlationID="' . $messageID . '">
<state>' . $state . '</state>
<stateMessage>' . $stateMessage . '</stateMessage>
</stuResponseMsg>';

        return new Response($response);

    }
}
