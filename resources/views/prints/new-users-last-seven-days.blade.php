@extends('layouts.print-layout')
@section('content')
    <br/>
    <p style="font-weight: 600; text-align: center; font-size: 18px">
        Novos Usuários Últimos 7 Dias
    </p>
    <br/>
    <table border="2" style="border-collapse: collapse; width: 100%">
        <tr>
            <th style="text-align: left">Quantidade de Usuários</th>
            <th style="text-align: right">Data</th>
        </tr>
        @foreach($users_last_seven_days as $user)
            <tr>
                <td>
                    {{ $user->qtde_users }}
                </td>
                <td style="text-align: right">{{ $user->day }}/{{ $user->month }}/{{ $user->year }}</td>
            </tr>
        @endforeach
    </table>
    <br/>
    <br/>
@endsection
