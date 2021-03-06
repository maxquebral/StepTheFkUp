<?php
declare(strict_types=1);

namespace StepTheFkUp\ApiToken\Tests\Decoders;

use Firebase\JWT\JWT;
use StepTheFkUp\ApiToken\Exceptions\InvalidApiTokenFromRequestException;
use StepTheFkUp\ApiToken\Decoders\JwtTokenDecoder;
use StepTheFkUp\ApiToken\Tests\AbstractJwtTokenTestCase;
use StepTheFkUp\ApiToken\Tokens\JwtApiToken;

final class JwtTokenDecoderTest extends AbstractJwtTokenTestCase
{
    /**
     * JwtTokenDecoder should return null if Authorization header not set.
     *
     * @return void
     *
     * @throws \StepTheFkUp\ApiToken\Exceptions\InvalidApiTokenFromRequestException
     */
    public function testJwtTokenNullIfAuthorizationHeaderNotSet(): void
    {
        self::assertNull((new JwtTokenDecoder([], static::$key))->decode($this->createServerRequest()));
    }

    /**
     * JwtTokenDecoder should return null if Authorization header doesn't start with "Bearer ".
     *
     * @return void
     *
     * @throws \StepTheFkUp\ApiToken\Exceptions\InvalidApiTokenFromRequestException
     */
    public function testJwtTokenNullIfDoesntStartWithBearer(): void
    {
        self::assertNull((new JwtTokenDecoder([], static::$key))->decode($this->createServerRequest([
            'HTTP_AUTHORIZATION' => 'SomethingElse'
        ])));
    }

    /**
     * JwtTokenDecoder should throw an exception if unable to decode token because token is invalid.
     *
     * @return void
     *
     * @throws \StepTheFkUp\ApiToken\Exceptions\InvalidApiTokenFromRequestException
     */
    public function testJwtTokenThrowExceptionIfUnableToDecodeToken(): void
    {
        $this->expectException(InvalidApiTokenFromRequestException::class);

        (new JwtTokenDecoder(['HS256'], 'different-key', 2))->decode($this->createServerRequest([
            'HTTP_AUTHORIZATION' => 'Bearer ' . $this->createToken()
        ]));
    }

    /**
     * @throws \StepTheFkUp\ApiToken\Exceptions\InvalidApiTokenFromRequestException
     */
    public function testJwtTokenDecodeSuccessfully(): void
    {
        foreach (static::$algos as $algo) {
            $key = static::$key;

            if ($this->isAlgoRs($algo)) {
                $key = $this->getOpenSslPublicKey();
            }

            $token = (new JwtTokenDecoder([$algo], $key))->decode($this->createServerRequest([
                'HTTP_AUTHORIZATION' => 'Bearer ' . $this->createToken($algo)
            ]));

            $payload = $token->getPayload();

            self::assertInstanceOf(JwtApiToken::class, $token);

            foreach (static::$tokenPayload as $key => $value) {
                self::assertArrayHasKey($key, $payload);
                self::assertEquals($value, $payload[$key]);
            }
        }
    }

    /**
     * Create JWT token for given algo.
     *
     * @param null|string $algo
     *
     * @return string
     */
    private function createToken(?string $algo = null): string
    {
        $key = static::$key;

        if ($algo !== null && $this->isAlgoRs($algo)) {
            $key = $this->getOpenSslPrivateKey();
        }

        return JWT::encode(static::$tokenPayload, $key, $algo ?? static::$defaultAlgo);
    }
}