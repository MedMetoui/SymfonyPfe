<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\UserFormType;
use App\Entity\Users;
use App\Repository\UsersRepository;


class UsersController extends AbstractController
{
    /**
     * @Route("/AddUser", name="AjouterUser")
     */
    public function AddUser(Request $requete): Response
    {
        //instance de Users
        $pers=new Users();
        $form=$this->createForm(UserFormType::class,$pers);
        $form->handleRequest($requete);
       //clic sur le bouton
       if($form->isSubmitted() && $form->isValid())
       {
           // $_FILES['img']['name'] == php
           // $_FILES['img']['tmp_name']
           /// Upload 
           $files=$form->get('image')->getData();
           //image.png
           $photoName=pathinfo($files->getClientOriginalName(),PATHINFO_FILENAME);// image
           $photo=$photoName.'.'.$files->guessExtension();// image.png
           $cnx=$this->getDoctrine()->getManager();
           //Move
           $files->move($this->getParameter('uploadUsers'),$photo);
            
           $pers->setImage($photo);
           // Persist
           $cnx->persist($pers);
           // Execution == flush()
           $cnx->flush();
       }
        return $this->render('users/Ajout.html.twig',[
            'frm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/login", name="Authentification")
     */
    public function Login(Request $req,UsersRepository $repUser): Response
    {
        $form=$this->createForm(UserFormType::class);
        if($req->get('connexion'))// connexion doit être le name de notre bouton de type submit
        {
         // récupération de notre formulaire user_form puisque le formulaire est généré
         $users=$req->get('user_form');// type de retourne array
         $login=$users['login'];$mdp=$users['mdp'];
         // la selection de notre Users by login et mdp saisie 
         // select * from users where login=$login and mdp=$mdp
         // findOne ==   limit 1
         // 
          $res=$repUser->findBy(array('login'=>$login,'mdp'=>$mdp));
          // si le user E dans la table 
          // count pour calculer le nbre des enregistrements dans Users 
          $nbuser=count($res);
          // isset() 
          // empty() vide ou non
          var_dump($res);
          if($nbuser>0)
          {

             
            foreach($res as $cle)
              {
            $role=$cle->getRole(); 
            echo $role;
              } 
              /*$role=$res->getRole(); 
             echo 'Role '.$role;*/
            // return $this->redirectToRoute('profile');
              
          }
          else{
              // name de route
              $this->redirectToRoute('AjouterUser');
          }
        }
        return $this->render('users/login.html.twig', [
            'frm' =>$form->createView(),
        ]);
    }

    

}
