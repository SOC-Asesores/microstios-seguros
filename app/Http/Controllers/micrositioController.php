<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use App\Mail\EmergencyCallReceived;
use Mailjet\Resources;


class micrositioController extends Controller
{
    public function broker($url){
        function eliminar_acentos($cadena){
                
                //Reemplazamos la A y a
                $cadena = str_replace(
                array('Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª'),
                array('A', 'A', 'A', 'A', 'a', 'a', 'a', 'a', 'a'),
                $cadena
                );

                //Reemplazamos la E y e
                $cadena = str_replace(
                array('É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê'),
                array('E', 'E', 'E', 'E', 'e', 'e', 'e', 'e'),
                $cadena );

                //Reemplazamos la I y i
                $cadena = str_replace(
                array('Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î'),
                array('I', 'I', 'I', 'I', 'i', 'i', 'i', 'i'),
                $cadena );

                //Reemplazamos la O y o
                $cadena = str_replace(
                array('Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô'),
                array('O', 'O', 'O', 'O', 'o', 'o', 'o', 'o'),
                $cadena );

                //Reemplazamos la U y u
                $cadena = str_replace(
                array('Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û'),
                array('U', 'U', 'U', 'U', 'u', 'u', 'u', 'u'),
                $cadena );

                //Reemplazamos la N, n, C y c
                $cadena = str_replace(
                array('Ñ', 'ñ', 'Ç', 'ç'),
                array('N', 'n', 'C', 'c'),
                $cadena
                );
                
                return $cadena;
            }
            function clean($string) {
                $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
                $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
                $string = strtolower($string);
                        
                return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
            }


        $registro = User::where('url_clean',$url)->get();

        if (@count($registro) <= 0) {
            $registro = User::where('url',$url)->get();
            if (@count($registro) >= 0) {
                if ($registro[0]["url_clean"] == null) { 
                    $registro = User::find($registro[0]["id"]);
                    $url = explode(" -  ", $registro->sucursal);
                    $url = explode(" - ", $url[0]);
                    $url = eliminar_acentos($url[0]);
                    $url = clean($url);
                    $registro->url_clean = $url;
                    $registro->save();

                    return Redirect::to('https://socasesores.com/micrositios-seguros/'.$url);
                }else{
                    return Redirect::to('https://socasesores.com/micrositios-seguros/'.$registro[0]["url_clean"]);
                }
            }
            
        }else{
            return view('micrositio',['registro' => $registro[0]]);
        }
        
    }

    public function dashboard($id){
        $registro = User::where('id',$id)->get();
        return view('dashboard',['registro' => $registro[0]]);
    }

    public function administrador(){
        $registro = User::where('type', 0)->get();
        $type = Auth::user()->type;
        return view('administrador',['registro' => $registro, 'type' => $type]);
    }

    public function certificado(Request $request){
        $id = $request->id;
        $user = User::find($id);
        $user->certificacion = $request->option;
        $user->save();
        return "save";
    }

    public function search(Request $request){
        $search = $request->search;
        if ($search == null) {
            $registro = User::where([
                ['type', 0],
            ])->get();
        }else{
            $registro = User::where([
                ['type', 0],
                ['sucursal', 'like', '%'.$search.'%'],
            ])->get();
        }
        $type = Auth::user()->type;
        return view('administrador',['registro' => $registro, 'type' => $type]);
    }
    public function sendMail(Request $request){
        $mj = new \Mailjet\Client("3ed1abd6eef1c2e815025a2801116c70", "4775bb3a9bcedb326bc355925aa04edf",true,['version' => 'v3.1']);

            // Define your request body
            $texto = 
            $body = [
                'Messages' => [
                    [
                        'From' => [
                            'Email' => "ingluisfelipe07@gmail.com",
                            'Name' => "Webmaster"
                        ],
                        'To' => [
                            [
                                'Email' => $request->email_cliente,
                                'Name' => $request->email_cliente
                            ]
                        ],
                        'Subject' => "Contacto desde el Micrositio",
                        'HTMLPart' => 'Estos son los datos del cliente:<ul>
                                            <li>Nombre: '.$request->name.'</li>
                                            <li>Teléfono: '.$request->phone.'</li>
                                            <li>Email: '.$request->email.'</li>
                                            <li>Tipo de Seguro: '.$request->type.'</li>
                                            <li>Mensaje: '.$request->message.'</li>
                                        </ul>'
                    ]
                ]
            ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
       
        $registro = User::where('url',$request->url)->get();
        return view('micrositio',['registro' => $registro[0]]);
    }
    public function update(Request $request){
        function clean($string) {
            $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
            $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
            $string = strtolower($string);
         
            return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
        }

        $id = Auth::id();
        $user = User::find($id);
        $url = clean($request->sucursal);

        if($request->file('logo')) {
            $file = $request->file('logo');
            $file_name = clean($file->getClientOriginalName());
            $logo = time().'_'.$file_name;

            // File upload location
            $location = 'img/brokers';

            // Upload file
            $file->move($location,$logo);
            $user->logo = $logo;
        }else{
           
        }

        if($request->file('imagen_1')) {
            $file = $request->file('imagen_1');
            $file_name = $file->getClientOriginalName();
            $file_name = trim($file_name);
            $imagen_1 = time().'_'.$file_name;

            // File upload location
            $location = 'img/brokers';

            // Upload file
            $file->move($location,$imagen_1);
            $user->imagen_1 = $imagen_1;
        }else{
           
        }

        if($request->file('imagen_2')) {
            $file = $request->file('imagen_2');
            $imagen_2 = time().'_'.$file->getClientOriginalName();

            // File upload location
            $location = 'img/brokers';

            // Upload file
            $file->move($location,$imagen_2);
            $user->imagen_2 = $imagen_2;
        }else{
           
        }

        if($request->file('oficina_1')) {
            $file = $request->file('oficina_1');
            $file_name = clean($file->getClientOriginalName());
            $oficina_1 = time().'_'.$file_name;

            // File upload location
            $location = 'img/oficinas';

            // Upload file
            $file->move($location,$oficina_1);
            $user->oficina_1 = $oficina_1;
        }else{
           
        }

        if($request->file('oficina_2')) {
            $file = $request->file('oficina_2');
            $file_name = clean($file->getClientOriginalName());
            $oficina_2 = time().'_'.$file_name;

            // File upload location
            $location = 'img/oficinas';

            // Upload file
            $file->move($location,$oficina_2);
            $user->oficina_2 = $oficina_2;
        }else{
           
        }

        if($request->file('oficina_3')) {
            $file = $request->file('oficina_3');
            $file_name = clean($file->getClientOriginalName());
            $oficina_3 = time().'_'.$file_name;

            // File upload location
            $location = 'img/oficinas';

            // Upload file
            $file->move($location,$oficina_3);
            $user->oficina_3 = $oficina_3;
        }else{
           
        }

        if($request->file('oficina_4')) {
            $file = $request->file('oficina_4');
            $file_name = clean($file->getClientOriginalName());
            $oficina_4 = time().'_'.$file_name;

            // File upload location
            $location = 'img/oficinas';

            // Upload file
            $file->move($location,$oficina_4);
            $user->oficina_4 = $oficina_4;
        }else{
           
        }

        if($request->file('oficina_5')) {
            $file = $request->file('oficina_5');
            $file_name = clean($file->getClientOriginalName());
            $oficina_5 = time().'_'.$file_name;

            // File upload location
            $location = 'img/oficinas';

            // Upload file
            $file->move($location,$oficina_5);
            $user->oficina_5 = $oficina_5;
        }else{
           
        }

        if($request->file('oficina_6')) {
            $file = $request->file('oficina_6');
            $file_name = clean($file->getClientOriginalName());
            $oficina_6 = time().'_'.$file_name;

            // File upload location
            $location = 'img/oficinas';

            // Upload file
            $file->move($location,$oficina_6);
            $user->oficina_6 = $oficina_6;
        }else{
           
        }
        $client = new Client();
        $res = $client->request('GET', 'https://maps.googleapis.com/maps/api/geocode/json?address='.$request->url_mapa.'&key=AIzaSyCKCFLBTkEow5a8eOSEp4-exz0xHO_ob6U');
        $res = json_decode($res->getBody());
        $lat = $res->results[0]->geometry->location->lat;
        $lng = $res->results[0]->geometry->location->lng;

        $user->sucursal = $request->sucursal;
        $user->url = $url;
        $user->producto_2 = $request->producto_2;
        $user->producto_3 = $request->producto_3;
        $user->producto_4 = $request->producto_4;
        $user->producto_5 = $request->producto_5;
        $user->producto_6 = $request->producto_6;
        $user->producto_7 = $request->producto_7;
        $user->producto_8 = $request->producto_8;
        $user->producto_9 = $request->producto_9;
        $user->producto_10 = $request->producto_10;
        $user->horario = $request->horario;
        $user->lat = $lat;
        $user->lng = $lng;
        $user->url_mapa = $request->url_mapa;
        $user->direccion = $request->direccion;
        $user->telefono = $request->telefono;
        $user->facebook = $request->facebook;
        $user->twitter = $request->twitter;
        $user->youtube = $request->youtube;
        $user->linkedin = $request->linkedin;
        $user->instagram = $request->instagram;


        $user->save();

        $registro = User::where('id',$id)->get();
        return view('dashboard',['registro' => $registro[0], 'update' => true]);
    }
}
