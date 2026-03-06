<?php
require __DIR__.'/vendor/autoload.php';

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;

$privateKey = InMemory::file(__DIR__.'/config/jwt/private_no_pass.pem');
$publicKey = InMemory::file(__DIR__.'/config/jwt/public_no_pass.pem');

$configuration = Configuration::forAsymmetricSigner(
    new Sha256(),
    $privateKey,
    $publicKey
);

$now = new \DateTimeImmutable();

$token = $configuration->builder()
    ->issuedBy('http://cart-microservice.test')
    ->permittedFor('http://cart-microservice.test')
    ->identifiedBy('test-token')
    ->issuedAt($now)
    ->expiresAt($now->modify('+1 hour'))
    ->withClaim('user_identifier', 'testuser') // <-- important
    ->withClaim('roles', ['ROLE_USER'])
    ->getToken($configuration->signer(), $configuration->signingKey());

echo $token->toString();
