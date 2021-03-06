<?php
declare(strict_types=1);

namespace StepTheFkUp\ApiToken\Encoders;

use StepTheFkUp\ApiToken\Exceptions\UnableToEncodeApiTokenException;
use StepTheFkUp\ApiToken\Interfaces\ApiTokenEncoderInterface;
use StepTheFkUp\ApiToken\Interfaces\ApiTokenInterface;
use StepTheFkUp\ApiToken\Interfaces\Tokens\ApiKeyApiTokenInterface;
use StepTheFkUp\ApiToken\Traits\ApiTokenEncoderTrait;

final class ApiKeyAsBasicAuthUsernameEncoder implements ApiTokenEncoderInterface
{
    use ApiTokenEncoderTrait;

    /**
     * Return encoded string representation of given API token.
     *
     * @param \StepTheFkUp\ApiToken\Interfaces\ApiTokenInterface $apiToken
     *
     * @return string
     *
     * @throws \StepTheFkUp\ApiToken\Exceptions\InvalidArgumentException If encoder doesn't support given apiToken
     * @throws \StepTheFkUp\ApiToken\Exceptions\UnableToEncodeApiTokenException If encoder fails to encode apiToken
     */
    public function encode(ApiTokenInterface $apiToken): string
    {
        $this->validateToken(ApiKeyApiTokenInterface::class, $apiToken);

        /** @var \StepTheFkUp\ApiToken\Interfaces\Tokens\ApiKeyApiTokenInterface $apiToken */
        $apiKey = $apiToken->getApiKey();

        if (empty($apiKey) === false) {
            return \base64_encode($apiKey);
        }

        throw new UnableToEncodeApiTokenException(\sprintf('In "%s", api key empty.', \get_class($this)));
    }
}