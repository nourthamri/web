<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\StudentRepository;
use App\Entity\Student;


class StudentController extends AbstractController
{
  
    #[Route('/student/add', name: 'app_addstudent')]
    public function addStudent(ManagerRegistry $manager)
    {
        $em= $manager->getManager();
        $student1=new Student();
        $student1->setUsername("Nour");
        $student1->setEmail("Nour@esprit.tn");

       
        $em->persist($student1);

        $student2=new Student();
        $student2->setUsername("NourMsd");
        $student2->setEmail("Nourmsd@esprit.tn");
       
        $em->persist($student2);
        $em->flush();
        return new Response("added succesfully",200);

        
    }
    #[Route('/student/getall', name: 'app_getallstudent')]
    public function getallStudent(StudentRepository $repo) 
    {
      $students= $repo->findAll();
      return $this->render('student/index.html.twig', [
        'students' =>$students ]);


    }
    #[Route('/student/update/{id}', name: 'app_updateStudent')]
   
 public function updateStudent(ManagerRegistry $manager,StudentRepository $repo , $id){
    $student1=$repo->find($id);
    $student1->setUsername('Taha');

    $em=$manager->getManager();
    $em->flush();

    return new Response("update succefully",200);


 }

 #[Route('/student/delete/{id}', name: 'app_deleteStudent')]
 public function deleteStudent(ManagerRegistry $manager,StudentRepository $repo , $id)
 {
    $student1=$repo->find($id);
    $em=$manager->getManager();
    $em->remove($student1);
    $em->flush();
    return $this->redirectToRoute('app_getallstudent');


 }
   


}
