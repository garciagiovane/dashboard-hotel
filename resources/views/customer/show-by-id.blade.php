<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Buscar Candidatos</title>
</head>

<body>
    @if ($error)
        <div>Não foi possível encontrar o cliente</div>
    @else
        @if ($customer)
            <div>
                <h2>Cliente</h2>
                <p>{{ $customer->name }}</p>
            </div>
        @endif
    @endif
</body>

</html>
