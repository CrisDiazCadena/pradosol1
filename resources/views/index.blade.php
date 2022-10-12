@if($errors->any())
    <ul>
        @foreach ($errors->all())
            <li>{{ $error }}</li>
        @endforeach
    </ul>
@endif
<form method="POST">
    @csrf
    <label>
        <input name="email" type="email" required autofocus value="{{ old('email')}}" placeholder="Email">
    </label>
    @error('email'){{ $message }} @enderror
    <br>
    <label>
        <input name="password" type="password" placeholder="Contraseña">
    </label>
    @error('password'){{ $message }} @enderror
    <br>
    <label>
    <input type="checkbox" name="remember"> Recordar Sesion
    </label><br>
    <button type="submit">Login</button>
</form>



Route::post('Login', funtion(){
    $credentials = request()->validate([
        'email' => ['required', 'email', 'string'],
        'password'['required', 'string']
    ]);

    $remember = dd(request()->filled('remember'));

    if (Auth::attempt($credentials, $remember)) {
        request()->session()->regenerate();
        return redirect()->intended(' ')
    };
    throw ValidationException::withMessages([
        'email' => __('auth.failed')
    ])

 });


//proteccion de rutas 
ruta definida -> middleware('auth'):

//redireccion si esta autenticado
ruta definida -> middleware('guest')