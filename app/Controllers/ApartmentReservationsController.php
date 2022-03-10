<?php

namespace App\Controllers;

use App\Database;
use App\Models\Apartment;
use App\Models\Reservation;
use App\Redirect;
use App\Views\View;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class ApartmentReservationsController
{
    public function show(array $vars): View
    {
        $apartmentId = (int)$vars['id'];
        $active = $_SESSION["fullName"];
        $activeId = $_SESSION["id"];

        $apartmentReservationsQuery = Database::connection()
            ->createQueryBuilder()
            ->select('*')
            ->from('apartment_reservations')
            ->where('apartment_id = ?')
            ->setParameter(0, $apartmentId)
            ->executeQuery()
            ->fetchAllAssociative();

        $reservations = [];
        foreach ($apartmentReservationsQuery as $apartmentReservationData) {
            $reservations [] = new Reservation(
                $apartmentReservationData['id'],
                $apartmentReservationData['apartment_id'],
                $apartmentReservationData['user_id'],
                $apartmentReservationData['reserved_from'],
                $apartmentReservationData['reserved_to']
            );
        }

        return new View("Reservations/show", [
            'reservations' => $reservations,
            'apartmentId' => $apartmentId,
            'active' => $active,
            'activeId' => $activeId
        ]);
    }

    public function reserve(array $vars): View
    {
        $apartmentId = (int)$vars['id'];
        $active = $_SESSION["fullName"];
        $activeId = $_SESSION["id"];

        $apartmentQuery = Database::connection()
            ->createQueryBuilder()
            ->select('*')
            ->from('apartments')
            ->where('id = ?')
            ->setParameter(0, $apartmentId)
            ->executeQuery()
            ->fetchAssociative();

        $apartment = new Apartment(
            $apartmentQuery['id'],
            $apartmentQuery['name'],
            $apartmentQuery['address'],
            $apartmentQuery['description'],
            $apartmentQuery['available_from'],
            $apartmentQuery['available_to'],
            $apartmentQuery['owner_id'],
            $apartmentQuery['price']
        );

        if (isset($_SESSION["inputReserveFrom"])) {
            $inputReserveFrom = $_SESSION["inputReserveFrom"];
            unset($_SESSION["inputReserveFrom"]);
        }else{
            $inputReserveFrom = "";
        }

        if (isset($_SESSION["inputReserveTo"])) {
            $inputReserveTo = $_SESSION["inputReserveTo"];
            unset($_SESSION["inputReserveTo"]);
        }else{
            $inputReserveTo = "";
        }

        if (isset($_SESSION["reservationConfirmed"])) {
            $reservationConfirmed = $_SESSION["reservationConfirmed"];
            unset($_SESSION["reservationConfirmed"]);
        }else{
            $reservationConfirmed = "false";
        }

        if (isset($_SESSION["overlap"])) {
            $datesOverlap = $_SESSION["overlap"];
            unset($_SESSION["overlap"]);
        } else {
            $datesOverlap = "";
        }

        if (isset($_SESSION["invalidFromDate"])) {
            $invalidFromDate = $_SESSION["invalidFromDate"];
            unset($_SESSION["invalidFromDate"]);
        } else {
            $invalidFromDate = "";
        }

        if (isset($_SESSION["invalidDates"])) {
            $invalidDates = $_SESSION["invalidDates"];
            unset($_SESSION["invalidDates"]);
        } else {
            $invalidDates = "";
        }
        $amountToPay = $_SESSION["amountToPay"];

        return new View("Reservations/reserve", [
            'active' => $active,
            'activeId' => $activeId,
            'apartment' => $apartment,
            'inputReserveFrom' => $inputReserveFrom,
            'inputReserveTo' => $inputReserveTo,
            'reservationConfirmed' => $reservationConfirmed,
            'datesOverlap' => $datesOverlap,
            'invalidFromDate' => $invalidFromDate,
            'invalidToDate' => $invalidToDate,
            'invalidDates' => $invalidDates,
            'amountToPay' => $amountToPay
        ]);
    }

    public function confirm(array $vars)
    {
        $active = $_SESSION["fullName"];
        $activeId = $_SESSION["id"];
        $apartmentId = (int)$vars['id'];
        $reserveFrom = $_POST['reserve_from'];
        $reserveTo = $_POST['reserve_to'];

        // reservedFrom and reservedTo fields are "required", no need to check if they are empty


        $dt = Carbon::now();
        $carbonReserveFrom = Carbon::parse($reserveFrom);
        $carbonReserveTo = Carbon::parse($reserveTo);
        $carbonToday = Carbon::parse($dt->toDateString());

        if(!$carbonReserveFrom->greaterThanOrEqualTo($carbonToday)){
            $_SESSION["inputReserveFrom"] = $reserveFrom;
            $_SESSION["inputReserveTo"] = $reserveTo;
            $_SESSION["invalidFromDate"] = "Invalid date, 'check-in' date must be later or equal with today's date";
            return new Redirect("/apartments/$apartmentId/reserve");
        }

        if(!$carbonReserveTo->greaterThan($carbonReserveFrom)){
            $_SESSION["inputReserveFrom"] = $reserveFrom;
            $_SESSION["inputReserveTo"] = $reserveTo;
            $_SESSION["invalidDates"] = "Invalid dates, 'check-in' date must be before 'check-out' date";
            return new Redirect("/apartments/$apartmentId/reserve");
        }

        $apartmentReservationsQuery = Database::connection()
            ->createQueryBuilder()
            ->select('*')
            ->from('apartment_reservations')
            ->andWhere('apartment_id = :id')
            ->setParameter('id', $apartmentId)
//            ->andWhere('reserved_from BETWEEN :from AND :to')
//            ->setParameter('from', $reserveFrom)
//            ->setParameter('to', $reserveTo)
            ->executeQuery()
            ->fetchAllAssociative();

        $reservedPeriods = [];
        foreach ($apartmentReservationsQuery as $reservationData) {
            $reservedPeriods [] = Carbon::parse($reservationData['reserved_from'])->daysUntil($reservationData['reserved_to']);
        }

        $potentialReservationPeriod = Carbon::parse($reserveFrom)->daysUntil($reserveTo);

        foreach ($reservedPeriods as $reservedPeriod) {
            if ($potentialReservationPeriod->overlaps($reservedPeriod)) {
                $_SESSION["overlap"] = "These dates are not available";
                $_SESSION["inputReserveFrom"] = $reserveFrom;
                $_SESSION["inputReserveTo"] = $reserveTo;
                return new Redirect("/apartments/$apartmentId/reserve");
            }
        }

        Database::connection()
            ->insert('apartment_reservations', [
                'apartment_id' => $apartmentId,
                'user_id' => $activeId,
                'reserved_from' => $reserveFrom,
                'reserved_to' => $reserveTo
            ]);
        $_SESSION["reservationConfirmed"] = "true";

        $apartmentQuery = Database::connection()
            ->createQueryBuilder()
            ->select('*')
            ->from('apartments')
            ->where('id = ?')
            ->setParameter(0, $apartmentId)
            ->executeQuery()
            ->fetchAssociative();

        $daysReserved = Carbon::parse($reserveFrom)->diffInDays(Carbon::parse($reserveTo));
        $_SESSION["amountToPay"] = (float) ($apartmentQuery['price'] * $daysReserved);

        return new Redirect("/apartments/$apartmentId/reserve");
    }

}