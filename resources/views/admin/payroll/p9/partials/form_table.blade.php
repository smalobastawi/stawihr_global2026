<table class="p9-table table table-bordered table-sm" style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr>
            <th rowspan="3" style="text-align: center; vertical-align: middle; width: 7%;">MONTH</th>
            <th colspan="17" style="text-align: center;">Kshs.</th>
        </tr>
        <tr>
            <th style="text-align: center;">Basic<br>Salary</th>
            <th style="text-align: center;">Benefits-<br>NonCash</th>
            <th style="text-align: center;">Value of<br>Quarters</th>
            <th style="text-align: center;">Total<br>Gross Pay</th>
            <th colspan="3" style="text-align: center;">Defined Contribution<br>Retirement Scheme</th>
            <th style="text-align: center;">Affordable<br>Housing Levy<br>(AHL)</th>
            <th style="text-align: center;">Social Health<br>Insurance Fund<br>(SHIF)</th>
            <th style="text-align: center;">Post Retirement<br>Medical Fund<br>(PRMF)</th>
            <th style="text-align: center;">Owner-Occupied<br>Interest</th>
            <th style="text-align: center;">Total Deductions<br>(Lower of E + F + G + H + I)</th>
            <th style="text-align: center;">Chargeable Pay<br>(D - J)</th>
            <th style="text-align: center;">Tax<br>Charged</th>
            <th style="text-align: center;">Personal<br>Relief</th>
            <th style="text-align: center;">Insurance<br>Relief</th>
            <th style="text-align: center;">PAYE Tax<br>(L - M - N)</th>
        </tr>
        <tr>
            <th style="text-align: center;">A</th>
            <th style="text-align: center;">B</th>
            <th style="text-align: center;">C</th>
            <th style="text-align: center;">D</th>
            <th style="text-align: center; font-size: 8px;">E1<br>30% of A</th>
            <th style="text-align: center; font-size: 8px;">E2<br>Actual</th>
            <th style="text-align: center; font-size: 8px;">E3<br>Fixed<br>30,000 p.m</th>
            <th style="text-align: center;">F</th>
            <th style="text-align: center;">G</th>
            <th style="text-align: center;">H</th>
            <th style="text-align: center;">I</th>
            <th style="text-align: center;">J</th>
            <th style="text-align: center;">K</th>
            <th style="text-align: center;">L</th>
            <th style="text-align: center;">M</th>
            <th style="text-align: center;">N</th>
            <th style="text-align: center;">O</th>
        </tr>
    </thead>
    <tbody>
        @foreach($salaryDetails as $salary)
            <tr>
                <th scope="row" style="text-align: center;">{{ date('M', strtotime($salary['month'] . '-01')) }}</th>
                <td style="text-align: right;">{{ number_format($salary['A'], 2) }}</td>
                <td style="text-align: right;">{{ number_format($salary['B'], 2) }}</td>
                <td style="text-align: right;">{{ number_format($salary['C'], 2) }}</td>
                <td style="text-align: right;">{{ number_format($salary['D'], 2) }}</td>
                <td style="text-align: right;">{{ number_format($salary['E1'], 2) }}</td>
                <td style="text-align: right;">{{ number_format($salary['E2'], 2) }}</td>
                <td style="text-align: right;">{{ number_format($salary['E3'], 2) }}</td>
                <td style="text-align: right;">{{ number_format($salary['F'], 2) }}</td>
                <td style="text-align: right;">{{ number_format($salary['G'], 2) }}</td>
                <td style="text-align: right;">{{ number_format($salary['H'], 2) }}</td>
                <td style="text-align: right;">{{ number_format($salary['I'], 2) }}</td>
                <td style="text-align: right;">{{ number_format($salary['J'], 2) }}</td>
                <td style="text-align: right;">{{ number_format($salary['K'], 2) }}</td>
                <td style="text-align: right;">{{ number_format($salary['L'], 2) }}</td>
                <td style="text-align: right;">{{ number_format($salary['M'], 2) }}</td>
                <td style="text-align: right;">{{ number_format($salary['N'], 2) }}</td>
                <td style="text-align: right;">{{ number_format($salary['O'], 2) }}</td>
            </tr>
        @endforeach
        <tr style="font-weight: bold; background-color: #f2f2f2;">
            <th style="text-align: center;">TOTAL</th>
            <td style="text-align: right;">{{ number_format($totals['A'], 2) }}</td>
            <td style="text-align: right;">{{ number_format($totals['B'], 2) }}</td>
            <td style="text-align: right;">{{ number_format($totals['C'], 2) }}</td>
            <td style="text-align: right;">{{ number_format($totals['D'], 2) }}</td>
            <td style="text-align: right;">{{ number_format($totals['E1'], 2) }}</td>
            <td style="text-align: right;">{{ number_format($totals['E2'], 2) }}</td>
            <td style="text-align: right;">{{ number_format($totals['E3'], 2) }}</td>
            <td style="text-align: right;">{{ number_format($totals['F'], 2) }}</td>
            <td style="text-align: right;">{{ number_format($totals['G'], 2) }}</td>
            <td style="text-align: right;">{{ number_format($totals['H'], 2) }}</td>
            <td style="text-align: right;">{{ number_format($totals['I'], 2) }}</td>
            <td style="text-align: right;">{{ number_format($totals['J'], 2) }}</td>
            <td style="text-align: right;">{{ number_format($totals['K'], 2) }}</td>
            <td style="text-align: right;">{{ number_format($totals['L'], 2) }}</td>
            <td style="text-align: right;">{{ number_format($totals['M'], 2) }}</td>
            <td style="text-align: right;">{{ number_format($totals['N'], 2) }}</td>
            <td style="text-align: right;">{{ number_format($totals['O'], 2) }}</td>
        </tr>
    </tbody>
</table>
