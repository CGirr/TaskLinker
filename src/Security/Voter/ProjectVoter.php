<?php

namespace App\Security\Voter;

use App\Entity\Employee;
use App\Entity\Project;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class ProjectVoter extends Voter
{
    public const EDIT = 'PROJECT_EDIT';
    public const VIEW = 'PROJECT_VIEW';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::VIEW])
            && $subject instanceof Project;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof Employee) {
            return false;
        }

        $project = $subject;

        return match ($attribute) {
            self::EDIT => $this->canEdit($project, $user),
            self::VIEW => $this->canView($project, $user),
            default => false,
        };
    }

    public function canView(Project $project, Employee $employee): bool
    {
        if (in_array('ROLE_ADMIN', $employee->getRoles(), true)) {
            return true;
        }

        return ($project->getMembers()->contains($employee));
    }

    private function canEdit(Project $project, Employee $employee): bool
    {
        return in_array('ROLE_ADMIN', $employee->getRoles());
    }
}
