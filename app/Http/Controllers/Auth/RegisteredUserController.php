<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        function clean($string) {
            $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
            $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
            $string = strtolower($string);
         
            return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
        }
        $url = clean($request->sucursal);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'sucursal' => $request->sucursal,
            'url' => $url,
            'telefono' => $request->telefono,
            'horario' => $request->horario,
            'direccion' => $request->direccion,
            'producto_1' => $request->producto_1,
            'producto_2' => $request->producto_2,
            'producto_3' => $request->producto_3,
            'producto_4' => $request->producto_4,
            'producto_5' => $request->producto_5,
            'producto_6' => $request->producto_6,
            'producto_7' => $request->producto_7,
            'producto_8' => $request->producto_8,
            'producto_9' => $request->producto_9,
            'producto_10' => $request->producto_10,
        ]);

        event(new Registered($user));

        Auth::login($user);

        $id = Auth::id();

        return redirect('/dashboard/'.$id);
    }

    public function insert(Request $request)
    {
        if ($request->token === "&elYYxm$*wm4") {
            $password = "soc_".rand();
            function clean($string) {
                $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
                $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
                $string = strtolower($string);
            
                return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
            }
            $url = clean($request->sucursal);

            if (isset($request->IdBroker)) { 
            }else{
                return "Falta el IdBroker";
            }
            if (isset($request->sucursal)) { 
            }else{
                return "Falta el Nombre de la Sucursal";
            }
            if (isset($request->email)) { 
                
            }else{
                return "Falta el Email";
            }
            if (isset($request->telefono)) { 

            }else{
                return "Falta el Teléfono";
            }
            $direccion = $request->calle." ".$request->n_exterior." ".$request->codigo_postal." ".$request->municipio." ".$request->estado;

            $registro_id = User::insertGetId([
                'id_sisec' => $request->IdBroker,
                'name' => $request->sucursal,
                'email' => $request->email,
                'direccion' => $direccion,
                'password' => Hash::make($password),
                'password_save' => $password,
                'sucursal' => $request->sucursal,
                'horario' => $request->horario,
                'url' => $url,
                'telefono' => $request->telefono,

            ]);

            return url('/')."/broker/".$url;
        }else{
            return "Token Incorrecto";
        }
        
    }

    public function update(Request $request, $idbroker)
    {
        if ($request->token === "&elYYxm$*wm4") {
            function clean($string) {
                $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
                $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
                $string = strtolower($string);
             
                return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
            }
            $user = User::where('id_sisec', $idbroker)->first();
            if ($user === null) {
                return "No se encontro registro";
            }
            $url = $user->url;
           
            if (isset($request->sucursal)) { 
                $url = clean($request->sucursal);
                $user->sucursal = $request->sucursal;
                $user->name = $request->sucursal;
                $user->url = $url;
            }else{
                
            }
            if (isset($request->email)) { 
                $user->email = $request->email;
            }else{
                
            }
            if (isset($request->telefono)) { 
                $user->telefono = $request->telefono;
            }else{
                
            }
            if (isset($request->direccion)) { 
                $user->direccion = $request->direccion;
            }else{
                
            }
            if (isset($request->horario)) { 
                $user->horario = $request->horario;
            }else{
                
            }
    
            $user->save();
    
            return url('/')."/broker/".$url;
        }else{
            return "Token Incorrecto";
        }

    }

}
