<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fav;
use App\Models\Vistor;
use App\Models\Place;

class FavController extends Controller {
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */

    public function index() { 
        if ( auth()->user() ) {
            $favs = Vistor::with( 'favs' )->where( 'VISTOR_ID', auth()->user()->vistor->VISTOR_ID )->get();
            $x = array();
            foreach ( $favs[ 0 ][ 'favs' ] as $index=>$favs_id ) {
                $x[ $index ] = $favs_id [ 'CAT_ID' ];
            }
            $places = Place::where( 'CAT_ID',  $x )->get();
            foreach ( $places as $p ) {
                $p[ 'IMG' ] = 'storage/places/'.$p[ 'IMG' ];
            }
            return view( 'visitor.allPlaces.all_places', compact( 'places' ) );
        }

    }

    public function store( Request $request ) {
        if ( auth()->user() ) {
            $visitor = Vistor::where( 'ACCOUNTID', auth()->user()->ACCOUNTID )->get();
            $visitor_id = $visitor[ 0 ]->VISTOR_ID;
            $input = $request->all();
            $z = array();
            foreach ( $input[ 'CAT_ID' ] as $index=>$in ) {
                $z[ $index ][ 'VISTOR_ID' ] = $visitor_id;
                $z[ $index ][ 'CAT_ID' ] = $in;
            }
            foreach ( $z as $fav ) {
                // return $fav;
                if (count( Fav::where( $fav )->get())==0  ) {
                    Fav::create( $fav ) ;
                }               
            }
            return redirect()->back();
         //   return redirect()->route('customer_fav.index');

        }
    }
}
