<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip Design</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }

        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            box-sizing: border-box;
            font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif, sans-serif;
            color: #333;
            font-size: 12 px;   
        }

        .a4-container {
            width: 210mm;
            height: 297mm;
            margin: auto;
            padding: 20mm;
            box-sizing: border-box;
            background: #fff;
            border: 1px solid #ccc;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .header {
            text-align: center;
            padding: 5px;
        
            color: #242424;
            font-size: 1.2em;
        }

        .section h3 {
            margin: 0;
            font-size: 14px;
            border-bottom: 1px solid #003366;
            padding-bottom: 5px;
            color: #003366;
        }

        .section {
            margin: 5px 0;
        }

        .employee-info, .payslip-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table th, table td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: center;
            font-size: 12px;
        }

        table th {
            /* background-color: #003366; */
            color: #0e0e0e;
        }

        .summary {
            text-align: right;
            font-weight: bold;
            margin-top: 10px;
        }

        .footer {
            text-align: center;
            font-size: 0.9em;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="a4-container">
        <!-- Header -->
        <div class="header">
            <img src="https://stawitech.com/wp-content/uploads/2020/04/logo-website.jpg" alt="Stawitech Logo" style="max-height: 50px; margin-bottom: 10px;">
            <h2>Stawitech Solutions Ltd</h2>
            <p>H7, Muthaiga North Dr. Nairobi KENYA</p>
        </div>

        <!-- Employee Info -->
        <div class="section">
            <h3>Employee Information</h3>
            <div class="employee-info">
                <div>
                    <p><strong>Name:</strong> Eugine Buluma</p>
                    <p><strong>ID:</strong> 12345678</p>
                   
                    <p><strong>Payroll #:</strong> EMP123</p>
                </div>
                <div>
                    <p><strong>Pay GRade:</strong> C3</p>
                    <p><strong>Pay Date:</strong> 27/11/2024</p>
                    <p><strong>Pay Type:</strong> Monthly</p>
                    <p><strong>Payment Method:</strong> Bank Transfer</p>
                </div>
            </div>
        </div>

        <!-- Earnings Table -->
        <div class="section">
            <h3>Earnings</h3>
            <table>
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Amount (KES)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Basic Income</td>
                        <td>120,000.00</td>
                    </tr>
                    <tr>
                        <td>House Allowance</td>
                        <td>50,000.00</td>
                    </tr>
                    <tr>
                        <td>Transport Allowance</td>
                        <td>30,000.00</td>
                    </tr>
                    <tr>
                        <td>Other Allowances</td>
                        <td>18,106.21</td>
                    </tr>
                    <tr>
                        <th>Total Earnings</th>
                        <th>218,590.50</th>
                    </tr>
                </tbody>
                
            </table>
        </div>

        <!-- Deductions Table -->
        <div class="section">
            <h3>Deductions</h3>
            <table>
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Amount (KES)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>PAYE</td>
                        <td>56,820.50 </td>
                    </tr>
                    <tr>
                        <td>SHIF</td>
                        <td>6,011.00 </td>
                    </tr>
                    <tr>
                        <td>NSSF</td>
                        <td>2,160.00</td>
                    </tr>
                    <tr>
                        <td>Housing Levy</td>
                        <td>3,279.00</td>
                    </tr>
                  
                    <tr>
                        <th>Total Deductions</th>
                        <th>68,270.50</th>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Summary -->
        <div class="summary">
            Net Pay: <span style="color: #003366;">KES 150,320.00</span>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>If you have any questions, contact HR at finance@stawitech.com</p>
        </div>
    </div>
</body>
</html>
