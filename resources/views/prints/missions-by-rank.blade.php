@extends('layouts.print-layout')
@section('content')
    <br/>
    <p style="font-weight: 600; text-align: center; font-size: 18px">
        Quantidade de Missões Completas nos últimos 7 dias por Rank
    </p>
    <br/>
    <table border="2" style="border-collapse: collapse; width: 100%">
        <tr>
            <th style="text-align: left">Rank</th>
            <th style="text-align: right">Quantidade</th>
        </tr>
        @foreach($missions_by_rank as $mission)
            <tr>
                <td>
                    {{ $mission->rank }}
                </td>
                <td style="text-align: right">{{ $mission->qtde_missoes }}</td>
            </tr>
        @endforeach
    </table>
    <br/>
    <br/>
@endsection
