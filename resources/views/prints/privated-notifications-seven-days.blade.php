@extends('layouts.print-layout')
@section('content')
    <br/>
    <p style="font-weight: 600; text-align: center; font-size: 18px">
        Qunatidade de Mensagens Privadas nos Últimos 7 Dias
    </p>
    <br/>
    <table border="2" style="border-collapse: collapse; width: 100%">
        <tr>
            <th style="text-align: left">Quantidade</th>
            <th style="text-align: right">Dia</th>
        </tr>
        @foreach($notifications as $notification)
            <tr>
                <td>
                    {{ $notification->qtde_notificacoes }}
                </td>
                <td style="text-align: right">{{ $notification->day }}/{{ $notification->month }}/{{ $notification->year }}</td>
            </tr>
        @endforeach
    </table>
    <br/>
    <br/>
@endsection
