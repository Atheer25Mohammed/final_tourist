<?php

namespace App\Http\Controllers;
use App\Models\Service;
use App\Models\Hotel;
use App\Models\Reservation;
use App\Models\Vistor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HotelController extends Controller {
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */

    public function index() {
        $hotels = Service::where( 'TYPE', 'HOTEL' )->get();
        foreach ( $hotels as $hotel ) {
            $hotel[ 'IMG' ] = 'storage/services/'.$hotel[ 'IMG' ];
        }
        if ( auth()->user() ) {
            if ( auth()->user()->ACCOUNT_TYPE == 'Admin' ) {
                return view( 'admin.hotels.all', compact( 'hotels' ) );
            }
        }
        return view( 'visitor.all_hotels.all_hotels', compact( 'hotels' ) );

    }

    public function bookingList() {
        if ( auth()->user() ) {
            if ( auth()->user()->ACCOUNT_TYPE == 'Admin' ) {
                 $bookings = Reservation::with( 'service' )
                ->join('service','service.SERVICE_ID','reservation.service_id')
                ->where( 'service.TYPE','=', 'HOTEL' )->get();                
                foreach ( $bookings as $index=>$booking ) {
                    $booking['service'][ 'IMG' ] = 'storage/services/'.$booking[ 'IMG' ];
                    $FNAME= Vistor::where( 'vistor_id', $bookings[$index]['vistor_id'])->get('FNAME');
                    $booking[ 'FNAME' ] =$FNAME[0]->FNAME;
                }
                return view( 'admin.hotels.booking_list', compact( 'bookings' ) );
            }
        }
    }
    /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */

    public function create() {
        //
    }

    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */

    public function store( Request $request ) {
        $input = $request->all();
        $v = Validator::make( $request->all(), [
            'SUPPLIER_ID' => [ 'nullable', 'string', 'max:255', ],
            'NAME' => [ 'required', 'string', 'max:255', ],
            'ADDRESS' => [ 'required', 'string', 'max:255', ],
            'LATITUDE' => [ 'required', 'string', 'max:255' ],
            'LONGITUDE' => [ 'required', 'string', 'max:255' ],
            'IMG' => 'required|file|image|mimes:jpg,jpeg,gif,png,webp,jfif',
            'TYPE' => [ 'required', 'string', 'max:10', ],
            'DESCRIPTION' => [ 'string', 'max:510' ],
            'STATE' => [ 'required'.'string', 'max:255' ]
        ] );
        if ( $v->fails() ) {
            return  redirect()->back()->withInput()->withErrors( $v->errors() );
        }
        $hotel = Service::create( $input );
        $hotel_id = $hotel->SERVICE_ID;
        $in[ 'SERVICE_ID' ] =  $hotel_id;
        $hotel_entity = Hotel::create( $in );
        if ( $hotel &&  $hotel_entity ) {
            return  redirect()->back()->with( 'sucess', 'تم حفظ بيانات الفندق بنجاح' );
        } else {
            return  redirect()->back()->withInput()->withErrors( 'flase', 'فشل في حفظ بيانات الفندق' );
        }
    }

    /**
    * Display the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */

    public function show( $id ) {
        $hotel = Service::find( $id );
        $hotel[ 'IMG' ] = 'storage/services/'.$hotel->IMG;
        return view( 'visitor.all_hotels.single_hotel', compact( 'hotel' ) );
    }

}
