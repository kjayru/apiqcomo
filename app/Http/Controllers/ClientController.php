<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Auth;

use App\User;
use App\Client;
use App\Franchise; 
use App\ClientPhoto;
use App\ClientConfiguration;
use App\ClientService;
use App\Service;
use App\Configuration;
use App\ClientsComentario;
use App\ClientHorario;
use App\ClientPolitica;
use App\ClientSubtipoComida;

class ClientController extends ApiController
{
    public function __construct()
    {
        $this->middleware('auth:api')->except(['index','store', 'verify', 'resend']);
        $this->middleware('transform.input:' . ClientTransformer::class)->only(['index','store', 'update']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        //$users = CustomerDetail::all();
        //$clientes = RestaurantDetail::where('customer_detail_id',$user->id)->get();
        /*$clientes = Client::all();
        $franchises = Franchise::all();
        $services = Service::all();
        
        $configurations = Configuration::all();
        return view('admin.paginas.clientes.index',['clients'=>$clientes,'franchises'=>$franchises,'services'=>$services,'configurations'=>$configurations]);
       */

      $clients = Client::all();
      return $this->showAll($clients);

    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        
        //cover
        $file = $request->file('cover');
        
        $input['img1'] = time().'.'.$file->getClientOriginalExtension();
        $destinationPath = public_path('/storage/client');
        $file->move($destinationPath, $input['img1']);
        
        //avatar
        $file2 = $request->file('logo');
        
        $input['img2'] = time().'.'.$file2->getClientOriginalExtension();
        $destinationPath2 = public_path('/storage/client');
        $file2->move($destinationPath2, $input['img2']);
        
        $client = new client();
        
        $client->name = $request->name;
        $client->address = $request->address;
        $client->country = $request->country;
        $client->city = $request->city;
        $client->province = $request->province;
        $client->cellphone = $request->cellphone;
        $client->email = $request->email;
        $client->cover =  $input['img1'];
        $client->logo =  $input['img2'];
        $client->sexo = $request->sexo;
        $client->cashier = $request->cashier;
        $client->status =2;
        $client->numesas =10;
        $client->franchise_id = $request->franchise_id;
        $client->latitude = $request->latitude;
        $client->longitude = $request->longitude;
        
        $client->save();
        
        return response()->json(['rpta' => 'ok','client_id'=>$client->id]);
        
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $services = Service::all();
        
        $configurations = Configuration::all();
        $clientes = Client::where('franchise_id',$id)->get();
        $franchise_id = $id;
        
        return view('admin.paginas.clientes.index',['clients'=>$clientes,'franchise_id'=>$franchise_id,'services'=>$services,'configurations'=>$configurations]);
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $cliente = Client::find($id);
        $services = ClientService::where('client_id',$id)->get();
        $configurations = ClientConfiguration::where('client_id',$id)->get();
        $fotos = ClientPhoto::where('client_id',$id)->get();
        return response()->json(['cliente'=>$cliente,'services'=>$services,'configurations'=>$configurations,'fotos'=>$fotos]);
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user_id = Auth::id();
        
        //cover
        $file = $request->file('cover');
        
        $input['img1'] = time().'.'.$file->getClientOriginalExtension();
        $destinationPath = public_path('/storage/client');
        $file->move($destinationPath, $input['img1']);
        
        //avatar
        $file2 = $request->file('logo');
        
        $input['img2'] = time().'.'.$file2->getClientOriginalExtension();
        $destinationPath2 = public_path('/storage/client');
        $file2->move($destinationPath2, $input['img2']);
        
        
        $client = Client::find($id);
        
        $client->name = $request->name;
        $client->address = $request->address;
        $client->country = $request->country;
        $client->city = $request->city;
        $client->province = $request->province;
        $client->cellphone = $request->cellphone;
        $client->email = $request->email;
        $client->cover =  $input['img1'];
        $client->logo =  $input['img2'];
        $client->sexo = $request->sexo;
        $client->cashier = $request->cashier;
        $client->status =2;
        $client->franchise_id = $request->franchise_id;
        $client->latitude = $request->latitude;
        $client->longitude = $request->longitude;
        
        $client->save();
        
        return response()->json(['rpta' => 'ok']);
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    
    public function foto(Request $request){
        
        
        if($request->file('foto')){
            foreach ($request->file('foto') as $photo) {
                $file = $photo;
                
                $input['imagename'] = time().'.'.$file->getClientOriginalExtension();
                $destinationPath = public_path('/storage/client');
                $file->move($destinationPath, $input['imagename']);
                
                $image = new ClientPhoto();
                $image->client_id = $request->client_id;
                $image->photo = $input['imagename'];
                $image->save();
                
            }
            
        }
    }
    
    public function fotoupdate(Request $request, $id){
        // ClientPhoto::where('client_id',$id)->delete();
        
        if($request->file('foto')){
            
            foreach ($request->file('foto') as $photo) {
                $file = $photo;
                
                $input['imagename'] = time().'.'.$file->getClientOriginalExtension();
                $destinationPath = public_path('/storage/client');
                $file->move($destinationPath, $input['imagename']);
                
                $image = new ClientPhoto();
                $image->client_id = $request->client_id;
                $image->photo = $input['imagename'];
                $image->save();
                
            }
            
        }
        
        return response()->json(['rpta'=>'ok']);
    }
    
    
    public function configuration(Request $request){
        
        foreach($request->configuration as $configura){
            
            $cf = new ClientConfiguration();
            $cf->client_id = $id;
            $cf->configuration_id = $configura;
            $cf->status = 2;
            $cf->save();
            
        }
        
        return response()->json(['rpta'=>'ok']);
        
    }
    
    public function configurationUpdate(Request $request, $id){
        ClientConfiguration::where('configuration_id',$id)->delete();
        foreach($request->configuration as $configura){
            
            $cf = new ClientConfiguration();
            $cf->client_id = $id;
            $cf->configuration_id = $configura;
            $cf->status = 2;
            $cf->save();
            
        }
        
        return response()->json(['rpta'=>'ok']);
        
    }
    
    public function service(Request $request){
        foreach($request->service as $servicio){
            
            $servicios = new ClientService();
            $servicios->client_id = $id;
            $servicios->service_id = $servicio;
            $servicios->status = 2;
            $servicios->save();
            
        }
        
        return response()->json(['rpta'=>'ok']);
    }
    
    public function serviceUpdate(Request $request, $id){
        
        ClientService::where('client_id',$id)->delete();
        foreach($request->service as $servicio){
            
            $servicios = new ClientService();
            $servicios->client_id = $id;
            $servicios->service_id = $servicio;
            $servicios->status = 2;
            $servicios->save();
            
        }
        
        return response()->json(['rpta'=>'ok']);
    }
    
    public function cambioestado(Request $request, $id){
        
        $client = Client::find($id);
        $client->status = $request->status;
        
        $client->save();
        
        return response()->json(['rpta'=>'ok']);
    }
    
    /*
     * esta funcion es para el API para cargar: los servicios, fotos y configuraciones de cada restaurante
     */
    public function getDetailRestaurante($id){
         
        $service = ClientService::where('client_id',$id)->get();
        $clientPhoto = ClientPhoto::where('client_id',$id)->get();
        $clientHorarios = ClientHorario::where('client_id',$id)->first();
        $clientPolitica = ClientPolitica::where('client_id',$id)->first();
        $clientSubtiposComida = ClientSubtipoComida::where('client_id',$id)->get();
        $puntajes = ClientsComentario::where('client_id',$id)->get()->take(250);
        if( $puntajes == null ){
            return ['puntajes'=>[]];
        }
        
        $ii = 0;
        $comentarios = [];
        $points = []; 
        $p_0 = 0;
        $p_1 = 0;
        $p_2 = 0;
        $p_3 = 0;
        $p_4 = 0;
        $p_5 = 0;
        
        foreach ($puntajes  as $puntaje) {
            if($ii < 10){                
                $user_id = $puntaje->user_id;
                $user = User::where('id',$user_id)->first();
                $comentario = [];
                $comentario['titulo'] = $user->name." ".$user->lastname;
                $comentario['fecha'] = $puntaje->updated_at;
                $comentario['descripcion'] = $puntaje->descripcion;
                $comentario['puntaje'] = $puntaje->puntuacion;
                $comentario['foto'] = $user->foto;
                $comentario['estado'] = $puntaje->estado;
                $comentarios[] = $comentario;
                $ii++;
            } 
            switch($puntaje->puntuacion){
                case 0:
                    $p_0 += 1;
                    break;
                case 1:
                    $p_1 += 1;
                    break;
                case 2:
                    $p_2 += 1;
                    break;
                case 3:
                    $p_3 += 1;
                    break;
                case 4:
                    $p_4 += 1;
                    break;
                case 5:
                    $p_5 += 1;
                    break;
            }
        }
        $points[] = ['valor'=>0,'cantidad'=>$p_0];
        $points[] = ['valor'=>1,'cantidad'=>$p_1];
        $points[] = ['valor'=>2,'cantidad'=>$p_2];
        $points[] = ['valor'=>3,'cantidad'=>$p_3];
        $points[] = ['valor'=>4,'cantidad'=>$p_4];
        $points[] = ['valor'=>5,'cantidad'=>$p_5];
        
        $arrHorarios = [];
        if( $clientHorarios != null ){
            $item['inicio'] = $clientHorarios->domingo_inicio;
            $item['final'] = $clientHorarios->domingo_final;
            $item['id_dia'] = 0;
            $arrHorarios[] = $item;

            $item1['inicio'] = $clientHorarios->lunes_inicio;
            $item1['final'] = $clientHorarios->lunes_final;
            $item1['id_dia'] = 1;
            $arrHorarios[] = $item1;

            $item2['inicio'] = $clientHorarios->martes_inicio;
            $item2['final'] = $clientHorarios->martes_final;
            $item2['id_dia'] = 2;
            $arrHorarios[] = $item2;

            $item3['inicio'] = $clientHorarios->miercoles_inicio;
            $item3['final'] = $clientHorarios->miercoles_final;
            $item3['id_dia'] = 3;
            $arrHorarios[] = $item3;

            $item4['inicio'] = $clientHorarios->jueves_inicio;
            $item4['final'] = $clientHorarios->jueves_final;
            $item4['id_dia'] = 4;
            $arrHorarios[] = $item4;

            $item5['inicio'] = $clientHorarios->viernes_inicio;
            $item5['final'] = $clientHorarios->viernes_final;
            $item5['id_dia'] = 5;
            $arrHorarios[] = $item5;

            $item6['inicio'] = $clientHorarios->sabado_inicio;
            $item6['final'] = $clientHorarios->sabado_final;
            $item6['id_dia'] = 6;
            $arrHorarios[] = $item6;
        }

        $response = [];
        $response['puntajes'] = $points; 
        $response['fotos'] = $clientPhoto;
        $response['horarios'] = $arrHorarios;
        
        $response['tipos_comida'] = $clientSubtiposComida;
        $response['caracteristicas'] = $service;
        $response['comentarios'] = $comentarios;
        
        if( $clientPolitica != null ){
            $response['precios'] = $clientPolitica->rango_precios; 
            $response['url'] = $clientPolitica->webpage;
            $response['politicas'] = $clientPolitica->politicas;
        }else{
            $response['precios'] = "no hay informacion"; 
            $response['url'] = "no hay informacion";
            $response['politicas'] = "no hay informacion";
        }
        
        return response()->json($response);
    }
    
}

