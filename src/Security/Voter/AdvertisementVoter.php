<?php

namespace App\Security\Voter;

use App\Entity\Advertisement;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class AdvertisementVoter extends Voter
{
    public function __construct(
        private Security $security
    ){}
    

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [Advertisement::EDIT, Advertisement::VIEW])
            && $subject instanceof \App\Entity\Advertisement;
    }
    /** @param Advertisement $subject */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        /** @var User $user */
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        // if (!$user instanceof UserInterface) {
        //     $vote?->addReason('The user must be logged in to access this resource.');

        //     return false;
        // }
        // vreau sa poata fi accesat si anonim, in caz ca ma razgandesc schimb de aici

        $isAuth = $user instanceof UserInterface;

        if($this->security->isGranted('ROLE_ADMIN')){
            return true;
        }
        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case Advertisement::EDIT:
                return $isAuth && 
                        ($subject->getAuthor()->getId()===$user->getId() || 
                        $this->security->isGranted('ROLE_EDITOR'));
            case Advertisement::VIEW:
                return true;
        }

        return false;
    }
}
