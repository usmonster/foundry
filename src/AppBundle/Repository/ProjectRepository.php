<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Project;

class ProjectRepository extends EntityRepository
{
    public function delete(Project $project) {
        $this->_em->createQuery("
            DELETE AppBundle\Entity\Step s
            WHERE s.project_id = :id
         ")->setParameters(array(
             'id' => $project->getId(),
         ))->execute();

        // BAD but there's a ManyToOne here.... need to fix it to manage deleted projects
        $this->_em->createQuery("
            DELETE AppBundle\Entity\CreditsHistory c
            WHERE c.project = :project
         ")->setParameters(array(
             'project' => $project,
         ))->execute();

        $this->_em->createQuery("
            DELETE AppBundle\Entity\Contributor c
            WHERE c.project = :project
         ")->setParameters(array(
             'project' => $project,
         ))->execute();

        $this->_em->createQuery("
            DELETE AppBundle\Entity\Vote v
            WHERE v.project = :project
         ")->setParameters(array(
             'project' => $project,
         ))->execute();

        $this->_em->createQuery("
            DELETE AppBundle\Entity\ProjectUpdate u
            WHERE u.project = :project
         ")->setParameters(array(
             'project' => $project,
         ))->execute();

        $this->_em->createQuery("
            DELETE AppBundle\Entity\Project p
            WHERE p.id = :id
         ")->setParameters(array(
             'id' => $project->getId(),
         ))->execute();
    }

    public function search($searchString) {
        return $this->_em->createQuery("
            SELECT p
            FROM AppBundle\Entity\Project p
            LEFT JOIN AppBundle\Entity\Family f WITH p.family = f.id
            WHERE
                p.title LIKE :search
                OR p.short_description LIKE :search
                OR f.name LIKE :search
            ORDER BY  p.endDate ASC, p.active DESC, p.title ASC
         ")->setParameters(array(
             'search' => "%".$searchString."%",
         ))->getResult();
    }

    public function participants($project) {
        return $this->_em->createQuery("
            SELECT c
            FROM AppBundle\Entity\Contributor c
            WHERE c.project = :project
         ")->setParameters(array(
             'project' => $project,
         ))->getResult();
    }

    public function voters($project) {
        return $this->_em->createQuery("
            SELECT ug.nickname
            FROM AppBundle\Entity\Vote v
            LEFT JOIN AppBundle\Entity\UserGoogle ug WITH v.user = ug.id
            WHERE v.project = :project
         ")->setParameters(array(
             'project' => $project,
         ))->getResult();
    }

    public function isUserInCrew($user, $project) {
        $data = $this->_em->createQuery("
            SELECT count(c)
            FROM AppBundle\Entity\Contributor c
            WHERE c.project = :project and c.user=:user
         ")->setParameters(array(
             'project' => $project,
             'user' => $user,
         ))->execute();
         //\Symfony\Component\VarDumper\VarDumper::dump($data[0][1]);die();
        if ($data[0][1]>0)
            $ret = true;
        else $ret = false;
        return $ret;
    }    

    public function findUpdates($project) {
        return $this->_em->createQuery("
            SELECT u
            FROM AppBundle\Entity\ProjectUpdate u
            WHERE u.project = :project
            ORDER by u.creationDate DESC
         ")->setParameters(array(
             'project' => $project,
         ))->getResult();
    }

    public function findMessages($project) {
            // LEFT JOIN AppBundle\Entity\Contributor c
            // WITH c.user = m.user AND c.project = :project
        return $this->_em->createQuery("
            SELECT m
            FROM AppBundle\Entity\ProjectMessage m
            WHERE m.project = :project 
            ORDER by m.creationDate ASC
         ")->setParameters(array(
             'project' => $project,
         ))->getResult();
    }

    public function projectSearchByFamilyId($familyId) {
        return $this->_em->createQuery("
                SELECT p as project,
                    (SELECT count(u.id) FROM AppBundle:ProjectUpdate u WHERE p.id = u.project) as cnt_updates,
                    (SELECT count(m.id) FROM AppBundle:ProjectMessage m WHERE p.id = m.project) as cnt_messages
                FROM AppBundle:Project p
                WHERE
                    p.family = :familyId
                ORDER BY p.active DESC, p.endDate ASC, p.creationDate DESC
        ")->setParameters(array(
             'familyId' => $familyId,
         ))->getResult();
    }

    public function projectByVotesFromFamily($familyId) {
        return $this->_em->createQuery("
            SELECT p.id, p.title, ug.nickname, count(v) as votes
            FROM AppBundle\Entity\Project p
            LEFT JOIN AppBundle\Entity\Vote v WITH v.project = p
            LEFT JOIN AppBundle\Entity\UserGoogle ug WITH p.user = ug.id
            WHERE p.family = :familyId
            GROUP BY p.id
            ORDER BY votes DESC
         ")->setParameters(array(
            'familyId' => $familyId,
         ))->getResult();
    }

    public function familySearchFromName($familyName) {
        return $this->_em->createQuery("
            SELECT f
            FROM AppBundle:Family f
            WHERE
                f.name = :search
         ")->setParameters(array(
             'search' => $familyName,
         ))->getResult();
    }
}
