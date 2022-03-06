<?php

namespace App\Controllers;

use App\Database;
use App\Models\Apartment;
use App\Redirect;
use App\Views\View;
use Carbon\Carbon;

class ApartmentsController
{
    public function index(): View
    {
        $apartmentsQuery = Database::connection()
            ->createQueryBuilder()
            ->select('*')
            ->from('apartments')
            ->executeQuery()
            ->fetchAllAssociative();

        $apartments = [];
        foreach ($apartmentsQuery as $apartmentData) {
            $apartments [] = new Apartment(
                $apartmentData['id'],
                $apartmentData['name'],
                $apartmentData['address'],
                $apartmentData['description'],
                $apartmentData['available_from'],
                $apartmentData['available_to'],
                $apartmentData['owner_id']
            );
        }

        return new View('Apartments/index', [
            'apartments' => $apartments
        ]);
    }

    public function show(array $vars): View
    {
        $apartmentId = (int)$vars['id'];
        $apartmentQuery = Database::connection()
            ->createQueryBuilder()
            ->select('*')
            ->from('apartments')
            ->where('id = ?')
            ->setParameter(0, (int)$vars['id'])
            ->executeQuery()
            ->fetchAssociative();

        $apartment = new Apartment(
            $apartmentQuery['id'],
            $apartmentQuery['name'],
            $apartmentQuery['address'],
            $apartmentQuery['description'],
            $apartmentQuery['available_from'],
            $apartmentQuery['available_to'],
            $apartmentQuery['owner_id']
        );

        return new View('Apartments/show', [
            'apartment' => $apartment
        ]);
    }

    public function create(array $vars): View
    {

        return new View('Apartments/create');
    }

    public function store(): Redirect
    {

        if (empty($_POST['name']) || empty($_POST['address']) || empty($_POST['description'])) {
            //empty name/address/description                   TODO error messages
            return new Redirect('/articles/create');
        }

        $dt = Carbon::now();

        if (empty($_POST['available_from'])) {
            $availableFrom = $dt->toDateString();
        } else {
            $availableFrom = $_POST['available_from'];
        }
        $endOfYear = $dt->endOfYear()->toDateString();
        if (empty($_POST['available_to'])) {
            $availableTo = $endOfYear;
        } else {
            $availableTo = $_POST['available_to'];
        }

        Database::connection()
            ->insert('apartments', [
                'name' => $_POST['name'],
                'address' => $_POST['address'],
                'description' => $_POST['description'],
                'available_from' => $availableFrom,
                'available_to' => $availableTo,
                'owner_id' => 1
            ]);

        return new Redirect('/apartments');
    }

}