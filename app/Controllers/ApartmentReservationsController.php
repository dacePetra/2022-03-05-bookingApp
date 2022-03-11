<?php

namespace App\Controllers;

use App\Database;
use App\Models\Apartment;
use App\Models\Reservation;
use App\Models\User;
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
            $apartmentQuery['price'],
            $apartmentQuery['rating']
        );

        $apartmentReservationsQuery = Database::connection()
            ->createQueryBuilder()
            ->select('*')
            ->from('apartment_reservations')
            ->where('apartment_id = ?')
            ->setParameter(0, $apartmentId)
            ->orderBy('reserved_from', 'asc')
            ->executeQuery()
            ->fetchAllAssociative();

        $reservations = [];
        $guestIds = [];
        foreach ($apartmentReservationsQuery as $apartmentReservationData) {
            $reservations [] = new Reservation(
                $apartmentReservationData['id'],
                $apartmentReservationData['apartment_id'],
                $apartmentReservationData['user_id'],
                $apartmentReservationData['reserved_from'],
                $apartmentReservationData['reserved_to']
            );
            $guestIds [] = $apartmentReservationData['user_id'];
        }
        $uniqueGuestIds = array_unique($guestIds);
        $guests =[];
        foreach ($uniqueGuestIds as $guestId){
            $userProfileQuery = Database::connection()
                ->createQueryBuilder()
                ->select('*')
                ->from('user_profiles')
                ->where('user_id = ?')
                ->setParameter(0, $guestId)
                ->executeQuery()
                ->fetchAssociative();

            $usersQuery = Database::connection()
                ->createQueryBuilder()
                ->select('*')
                ->from('users')
                ->where('id = ?')
                ->setParameter(0, $guestId)
                ->executeQuery()
                ->fetchAssociative();

            $guests [] = new User(
                $userProfileQuery['name'],
                $userProfileQuery['surname'],
                $userProfileQuery['birthday'],
                $usersQuery['email'],
                $usersQuery['password'],
                $usersQuery['created_at'],
                $usersQuery['id']
            );
        }

        return new View("Reservations/show", [
            'reservations' => $reservations,
            'guests' => $guests,
            'apartment' => $apartment,
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
            $apartmentQuery['price'],
            $apartmentQuery['rating']
        );

        $emptyInputDates = "";
        if (isset($_SESSION["emptyInputDates"])) {
            $emptyInputDates = $_SESSION["emptyInputDates"];
            unset($_SESSION["emptyInputDates"]);
        }

        $invalidFromDate = "";
        if (isset($_SESSION["invalidFromDate"])) {
            $invalidFromDate = $_SESSION["invalidFromDate"];
            unset($_SESSION["invalidFromDate"]);
        }

        $invalidDates = "";
        if (isset($_SESSION["invalidDates"])) {
            $invalidDates = $_SESSION["invalidDates"];
            unset($_SESSION["invalidDates"]);
        }

        $datesOverlap = "";
        if (isset($_SESSION["overlap"])) {
            $datesOverlap = $_SESSION["overlap"];
            unset($_SESSION["overlap"]);
        }

        $reservationConfirmed = "false";
        if (isset($_SESSION["reservationConfirmed"])) {
            $reservationConfirmed = $_SESSION["reservationConfirmed"];
            unset($_SESSION["reservationConfirmed"]);
        }

        $amountToPay = $_SESSION["amountToPay"];

        // Setting disabled dates
        $apartmentReservationsQuery = Database::connection()
            ->createQueryBuilder()
            ->select('*')
            ->from('apartment_reservations')
            ->andWhere('apartment_id = :id')
            ->setParameter('id', $apartmentId)
            ->executeQuery()
            ->fetchAllAssociative();

        $reservedDates = [];
        foreach ($apartmentReservationsQuery as $reservationData) {
            $reserveFrom = $reservationData['reserved_from'];
            //One day from end must be subtracted, because if one guest checks out on date X, another one CAN check in on the same date.
            $reserveTo = Carbon::parse($reservationData['reserved_to'])->subDay()->toDateString();
            $reservedPeriod = Carbon::parse($reserveFrom)->daysUntil($reserveTo);;
            foreach ($reservedPeriod as $reservedDate){
                $reservedDates [] = $reservedDate->format('m-d-Y');
            }
        }
        $today = Carbon::now()->format('m-d-Y');

        return new View("Reservations/reserve", [
            'active' => $active,
            'activeId' => $activeId,
            'apartment' => $apartment,
            'emptyInputDates' => $emptyInputDates,
            'reservationConfirmed' => $reservationConfirmed,
            'datesOverlap' => $datesOverlap,
            'invalidFromDate' => $invalidFromDate,
            'invalidDates' => $invalidDates,
            'amountToPay' => $amountToPay,
            'reservedDates' => $reservedDates,
            'today' => $today
        ]);
    }

    public function confirm(array $vars): Redirect
    {
        $active = $_SESSION["fullName"];
        $activeId = $_SESSION["id"];
        $apartmentId = (int)$vars['id'];

        // Converting POST date format to Y-m-d
        $dateFrom = explode("/", $_POST['reserve_from']);
        $reserveFrom = $dateFrom[2]."-".$dateFrom[0]."-".$dateFrom[1];
        $dateTo = explode("/", $_POST['reserve_to']);
        $reserveTo = $dateTo[2]."-".$dateTo[0]."-".$dateTo[1];

        // Validation: are reserveFrom & reserveTo dates filled in?
        if(empty($_POST['reserve_from']) || empty($_POST['reserve_to'])){
            $_SESSION["emptyInputDates"] = "Both 'check-in' and 'check-out' dates must be filled in";
            return new Redirect("/apartments/$apartmentId/reserve");
        }

        //Validation: is reserveFrom date grater than or equal to today's date?
        $dt = Carbon::now();
        $carbonReserveFrom = Carbon::parse($reserveFrom);
        $carbonReserveTo = Carbon::parse($reserveTo);
        $carbonToday = Carbon::parse($dt->toDateString());

        if(!$carbonReserveFrom->greaterThanOrEqualTo($carbonToday)){
            $_SESSION["invalidFromDate"] = "Invalid date, 'check-in' date must be greater or equal to today's date";
            return new Redirect("/apartments/$apartmentId/reserve");
        }

        //Validation: is reserveTo date grater than reserveFrom date?
        if(!$carbonReserveTo->greaterThan($carbonReserveFrom)){
            $_SESSION["invalidDates"] = "Invalid dates, 'check-out' date must be greater than 'check-in' date";
            return new Redirect("/apartments/$apartmentId/reserve");
        }

        //Validation: check in database if all days in potential reservation period are available?
        $apartmentReservationsQuery = Database::connection()
            ->createQueryBuilder()
            ->select('*')
            ->from('apartment_reservations')
            ->andWhere('apartment_id = :id')
            ->setParameter('id', $apartmentId)
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

    public function delete (array $vars): Redirect
    {
        $reservationId = (int)$vars['id'];
        $activeId = $_SESSION["id"];

        $reservationQuery = Database::connection()
            ->createQueryBuilder()
            ->select('*')
            ->from('apartment_reservations')
            ->where('id = ?')
            ->setParameter(0, $reservationId)
            ->executeQuery()
            ->fetchAssociative();

        $apartmentId = $reservationQuery['apartment_id'];

        $reservationQuery = Database::connection()
            ->createQueryBuilder()
            ->select('*')
            ->from('apartments')
            ->where('id = ?')
            ->setParameter(0, $apartmentId)
            ->executeQuery()
            ->fetchAssociative();

        $ownerId = $reservationQuery['owner_id'];
        if ($activeId == $ownerId) {
            Database::connection()
                ->delete('apartment_reservations', ['id' => $reservationId]);
        }
        return new Redirect("/reservations/{$apartmentId}/show");
    }

}