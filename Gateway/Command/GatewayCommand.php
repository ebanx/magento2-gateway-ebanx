<?php
namespace DigitalHub\Ebanx\Gateway\Command;

use Magento\Framework\Phrase;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferFactoryInterface;
use Magento\Payment\Gateway\Request;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Gateway\Response;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Payment\Gateway\Validator\ValidatorInterface;

class GatewayCommand implements CommandInterface
{
    /**
     * @var BuilderInterface
     */
    private $requestBuilder;

    /**
     * @var TransferFactoryInterface
     */
    private $transferFactory;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var HandlerInterface
     */
    private $handler;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param BuilderInterface $requestBuilder
     * @param TransferFactoryInterface $transferFactory
     * @param ClientInterface $client
     * @param \DigitalHub\Ebanx\Logger\Logger $logger
     * @param HandlerInterface $handler
     * @param ValidatorInterface $validator
     */
    public function __construct(
        BuilderInterface $requestBuilder,
        TransferFactoryInterface $transferFactory,
        ClientInterface $client,
        \DigitalHub\Ebanx\Logger\Logger $logger,
        HandlerInterface $handler = null,
        ValidatorInterface $validator = null
    ) {
        $this->requestBuilder = $requestBuilder;
        $this->transferFactory = $transferFactory;
        $this->client = $client;
        $this->handler = $handler;
        $this->validator = $validator;
        $this->logger = $logger;
        $this->logger->info('TESTE COMMAND :: __construct');
    }

    public function execute(array $commandSubject)
    {
        $this->logger->info('TESTE COMMAND :: execute');

        // @TODO implement exceptions catching
        $transferO = $this->transferFactory->create(
            $this->requestBuilder->build($commandSubject)
        );

        $response = $this->client->placeRequest($transferO);
        if ($this->validator !== null) {
            $result = $this->validator->validate(
                array_merge($commandSubject, ['response' => $response])
            );
            if (!$result->isValid()) {
                $this->logExceptions($result->getFailsDescription());

                $errorMessages = [];
                foreach($result->getFailsDescription() as $failPhrase){
                    $errorMessages[] = (string)$failPhrase;
                }

                throw new CommandException(
                    __(implode("; ", $errorMessages))
                );
            }
        }

        if ($this->handler) {
            $this->handler->handle(
                $commandSubject,
                $response
            );
        }
    }

    /**
     * @param Phrase[] $fails
     * @return void
     */
    private function logExceptions(array $fails)
    {
        foreach ($fails as $failPhrase) {
            $this->logger->critical((string) $failPhrase);
        }
    }
}
