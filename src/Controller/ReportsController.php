<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\BookRepository;
use App\Repository\AuthorRepository;
use App\Repository\EditorialRepository;

class ReportsController extends AbstractController
{
    #[Route('/reports', name: 'reports', methods: ['GET'])]
    public function index(BookRepository $bookRepository, AuthorRepository $authorRepository, EditorialRepository $editorialRepository): Response
    {
        //get rows from database
        $books = $bookRepository->findAll();
        $authors = $authorRepository->findAll();
        $editorials = $editorialRepository->findAll();

        $delimiter = ","; 
        $filename = "books_data_" . date('Y-m-d') . ".csv"; 
          
        // Create a file pointer 
        $f = fopen('php://temp', 'w'); 
          
        // Set column headers 
        $name = array('Books List'); 
        fputcsv($f, $name, $delimiter); 

        $fields = array('Id', 'Title', 'Author', 'Editorial', 'Published'); 
        fputcsv($f, $fields, $delimiter); 

        //set data
        foreach ($books as $key => $book) {
            $lineData = array($key+1, $book->getTitle(), $book->getAuthor(), $book->getEditorial(), $book->getPublishedDate()->format("M d Y")); 
            fputcsv($f, $lineData, $delimiter); 
        }
        $name = array('Author List'); 
        fputcsv($f, $name, $delimiter); 

        // Set column headers 
        $fields = array('Id', 'Name', 'Last Name', 'Phone Number', 'Address', 'Country'); 
        fputcsv($f, $fields, $delimiter); 

        //set data
        foreach ($authors as $key => $author) {
            $lineData = array($key+1, $author->getName(), $author->getLastName(), $author->getPhoneNumber(), $author->getAddress(), $author->getCountry()); 
            fputcsv($f, $lineData, $delimiter); 
        }
    
        $name = array('Editorial List'); 
        fputcsv($f, $name, $delimiter); 
        // Set column headers 
        $fields = array('Id', 'Name', 'Address', 'Country', 'Phone Number'); 
        fputcsv($f, $fields, $delimiter); 

        //set data
        foreach ($editorials as $key => $editorial) {
            $lineData = array($key+1, $editorial->getName(), $editorial->getAddress(), $editorial->getCountry(), $editorial->getPhoneNumber()); 
            fputcsv($f, $lineData, $delimiter); 
        }
    
    
        fputcsv($f, array('Number of Books in Catalog', count($bookRepository->findAll())));
        fputcsv($f, array('Number of Authors in Catalog',count($authorRepository->findAll())));
        fputcsv($f, array('Number of Authors in Catalog',count($editorialRepository->findAll())));
        fseek($f, 0); 
     
        $response = new Response(stream_get_contents($f), 200);
        $response->headers->set('Content-Type', 'text/csv');
        //it's gonna output in a testing.csv file
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '";');

        return $response;
    }
}
