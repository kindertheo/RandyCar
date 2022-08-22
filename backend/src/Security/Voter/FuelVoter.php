<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class FuelVoter extends Voter
{
    public const EDIT = 'EDIT';
    public const CREATE = 'CREATE';
    public const DELETE = 'DELETE';
    public const VIEW = 'VIEW';

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW, self::CREATE, self::DELETE])
            && $subject instanceof \App\Entity\Fuel;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }
        var_dump($subject);
        ob_flush();
        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                $this->canEdit($subject, $user);
                break;
            case self::VIEW:
                $this->canView($subject, $user);
                break;
            case self::CREATE:
                $this->canCreate($subject, $user);
                break;
            case self::DELETE:
                $this->canDelete($subject, $user);
                break;
        }

        return false;
    }

    private function canView(Fuel $fuel, User $user): bool
    {
        return in_array('ROLE_USER', $user->getRoles());
    }

    private function canEdit(Fuel $fuel, User $user): bool
    {
        return in_array('ROLE_ADMIN', $user->getRoles());
    }

    private function canCreate(Fuel $fuel, User $user): bool
    {
        return in_array('ROLE_ADMIN', $user->getRoles());
    }

    private function canDelete(Fuel $fuel, User $user): bool
    {
        return in_array('ROLE_ADMIN', $user->getRoles());
    }

}
