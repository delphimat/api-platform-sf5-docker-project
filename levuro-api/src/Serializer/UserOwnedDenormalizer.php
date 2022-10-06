<?php

namespace App\Serializer;

use App\Entity\UserOwnedInterface;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Exception\BadMethodCallException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\ExtraAttributesException;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Exception\RuntimeException;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;

class UserOwnedDenormalizer implements ContextAwareDenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    private const ALREADY_CALLED_DENORMALIZER = 'UserOwnedDenormalizerCalled';

    public function __construct(private Security $security, private UserRepository $userRepo)
    {
    }

    public function supportsDenormalization($data, string $type, string $format = null, array $context = []): bool
    {
        $reflectionClass = new \ReflectionClass($type);
        $alreadyCalled = $data[self::ALREADY_CALLED_DENORMALIZER] ?? false;
        return $reflectionClass->implementsInterface(UserOwnedInterface::class) && $alreadyCalled === false;
    }

    public function denormalize($data, string $type, string $format = null, array $context = []): UserOwnedInterface
    {
        $data[self::ALREADY_CALLED_DENORMALIZER] = true;
        /** @var UserOwnedInterface $obj */
        $obj = $this->denormalizer->denormalize($data, $type, $format, $context);

        $obj->setUser($this->userRepo->findOneBy(['username' => $this->security->getUser()->getUsername()]));
        return $obj;
    }
}
