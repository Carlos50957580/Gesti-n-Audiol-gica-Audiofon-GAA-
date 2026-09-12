<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historia Clínica #{{ $clinicalRecord->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12px;
            color: #000;
            background: #fff;
            padding: 20px;
        }
        
        .print-container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        /* ============================================ */
        /* HEADER - DATOS DE LA EMPRESA                 */
        /* ============================================ */
        .header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        
        .header .company-name {
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        
        .header .company-rnc {
            font-size: 12px;
            font-weight: 600;
        }
        
        .header .company-info {
            font-size: 11px;
            margin-top: 3px;
        }
        
        .header .company-slogan {
            font-size: 12px;
            font-style: italic;
            margin-top: 2px;
        }
        
        /* ============================================ */
        /* TÍTULO DE LA HISTORIA CLÍNICA               */
        /* ============================================ */
        .title-section {
            text-align: center;
            margin: 10px 0 15px 0;
        }
        
        .title-section h2 {
            font-size: 18px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 3px;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
            display: inline-block;
        }
        
        /* ============================================ */
        /* DATOS GENERALES DEL PACIENTE                */
        /* ============================================ */
        .section {
            margin-bottom: 12px;
        }
        
        .section-title {
            font-weight: 700;
            font-size: 13px;
            text-transform: uppercase;
            border-bottom: 1px solid #000;
            padding-bottom: 3px;
            margin-bottom: 6px;
            background: #f0f0f0;
            padding: 4px 8px;
        }
        
        .data-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3px 15px;
            padding: 5px 8px;
        }
        
        .data-grid .field {
            display: flex;
            align-items: baseline;
        }
        
        .data-grid .label {
            font-weight: 600;
            min-width: 100px;
        }
        
        .data-grid .value {
            font-weight: 400;
        }
        
        .data-grid-full {
            display: block;
            padding: 5px 8px;
        }
        
        .data-grid-full .field {
            display: flex;
            align-items: baseline;
            margin-bottom: 2px;
        }
        
        .data-grid-full .label {
            font-weight: 600;
            min-width: 120px;
        }
        
        .data-grid-full .value {
            font-weight: 400;
        }
        
        /* ============================================ */
        /* SIGNOS VITALES - TABLA                      */
        /* ============================================ */
        .vital-signs-table {
            width: 100%;
            border-collapse: collapse;
            margin: 5px 0;
            font-size: 11px;
        }
        
        .vital-signs-table td {
            border: 1px solid #000;
            padding: 4px 8px;
        }
        
        .vital-signs-table .label-cell {
            font-weight: 700;
            background: #f5f5f5;
            width: 18%;
        }
        
        .vital-signs-table .value-cell {
            width: 32%;
        }
        
        /* ============================================ */
        /* EXPLORACIÓN FÍSICA Y OTRAS SECCIONES        */
        /* ============================================ */
        .text-content {
            padding: 4px 8px;
            line-height: 1.5;
            text-align: justify;
        }
        
        .text-content ul {
            padding-left: 20px;
            list-style: disc;
        }
        
        .text-content ul li {
            margin-bottom: 2px;
        }
        
        /* ============================================ */
        /* TRATAMIENTO - LISTA                        */
        /* ============================================ */
        .treatment-list {
            padding: 4px 8px;
            list-style: none;
        }
        
        .treatment-list li {
            padding: 2px 0;
            border-bottom: 1px dashed #ddd;
        }
        
        .treatment-list li:last-child {
            border-bottom: none;
        }
        
        /* ============================================ */
        /* FOOTER                                     */
        /* ============================================ */
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #000;
            text-align: center;
            font-size: 10px;
        }
        
        .footer .footer-text {
            font-style: italic;
        }
        
        .footer .footer-company {
            font-weight: 600;
            margin-top: 3px;
        }
        
        /* ============================================ */
        /* PRINT STYLES                               */
        /* ============================================ */
        @media print {
            body {
                padding: 10px;
                font-size: 11px;
            }
            
            .no-print {
                display: none !important;
            }
            
            .section-title {
                background: #f0f0f0 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            .vital-signs-table .label-cell {
                background: #f5f5f5 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            .header {
                border-bottom: 3px double #000 !important;
            }
            
            .title-section h2 {
                border-bottom: 2px solid #000 !important;
            }
            
            .section-title {
                border-bottom: 1px solid #000 !important;
            }
            
            .vital-signs-table td {
                border: 1px solid #000 !important;
            }
            
            .footer {
                border-top: 1px solid #000 !important;
            }
        }
        
        /* ============================================ */
        /* BOTÓN DE IMPRESIÓN                          */
        /* ============================================ */
        .print-btn {
            display: inline-block;
            background: #405189;
            color: #fff;
            border: none;
            padding: 10px 25px;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            margin-bottom: 15px;
        }
        
        .print-btn:hover {
            background: #2d3e7a;
        }
    </style>
</head>
<body>
    <div class="print-container">
        
        <!-- Botón de impresión (solo en pantalla) -->
        <div class="no-print" style="text-align:center;margin-bottom:15px;">
            <button class="print-btn" onclick="window.print()">
                <i class="ri-printer-line"></i> Imprimir Historia Clínica
            </button>
            <a href="{{ route('clinical-records.show', $clinicalRecord) }}" 
               class="print-btn" 
               style="background:#6c757d;text-decoration:none;display:inline-block;">
                Volver
            </a>
        </div>

        <!-- ============================================ -->
        <!-- HEADER - DATOS DE LA EMPRESA                 -->
        <!-- ============================================ -->
        <div class="header">
            <div class="company-name">{{ $company['name'] ?? 'SAP PROSAUD' }}</div>
            @if($company['rnc'])
                <div class="company-rnc">RNC: {{ $company['rnc'] }}</div>
            @endif
            @if($company['address'])
                <div class="company-info">{{ $company['address'] }}</div>
            @endif
            @if($company['phone'])
                <div class="company-info">Tel: {{ $company['phone'] }}</div>
            @endif
            @if($company['slogan'])
                <div class="company-slogan">{{ $company['slogan'] }}</div>
            @endif
        </div>

        <!-- ============================================ -->
        <!-- TÍTULO                                      -->
        <!-- ============================================ -->
        <div class="title-section">
            <h2>Historia Clínica</h2>
        </div>

        <!-- ============================================ -->
        <!-- DATOS GENERALES DEL PACIENTE                 -->
        <!-- ============================================ -->
        <div class="section">
            <div class="section-title">Datos Generales del Paciente</div>
            <div class="data-grid">
                <div class="field">
                    <span class="label">Nombre:</span>
                    <span class="value">{{ $clinicalRecord->patient->full_name ?? 'N/A' }}</span>
                </div>
                <div class="field">
                    <span class="label">Sexo:</span>
                    <span class="value">{{ $clinicalRecord->patient->gender === 'M' ? 'Masculino' : ($clinicalRecord->patient->gender === 'F' ? 'Femenino' : 'N/A') }}</span>
                </div>
                <div class="field">
                    <span class="label">Fecha de Nacimiento:</span>
                    <span class="value">{{ $clinicalRecord->patient->birth_date ? \Carbon\Carbon::parse($clinicalRecord->patient->birth_date)->format('d-m-Y') : 'N/A' }}</span>
                </div>
                <div class="field">
                    <span class="label">Edad:</span>
                    <span class="value">{{ $clinicalRecord->patient->age ?? 'N/A' }} Años</span>
                </div>
                <div class="field">
                    <span class="label">Cédula:</span>
                    <span class="value">{{ $clinicalRecord->patient->cedula ?? 'N/A' }}</span>
                </div>
                <div class="field">
                    <span class="label">Teléfono:</span>
                    <span class="value">{{ $clinicalRecord->patient->phone ?? 'N/A' }}</span>
                </div>
                <div class="field" style="grid-column: span 2;">
                    <span class="label">Dirección:</span>
                    <span class="value">{{ $clinicalRecord->patient->address ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        <!-- ============================================ -->
        <!-- DATOS DE LA ADMISIÓN                         -->
        <!-- ============================================ -->
        <div class="section">
            <div class="section-title">Datos de la Admisión</div>
            <div class="data-grid">
                <div class="field">
                    <span class="label">Fecha Historia:</span>
                    <span class="value">{{ $clinicalRecord->consultation_date ? $clinicalRecord->consultation_date->format('d-m-Y') : 'N/A' }}</span>
                </div>
                <div class="field">
                    <span class="label">Médico:</span>
                    <span class="value">{{ $clinicalRecord->doctor->name ?? 'N/A' }}</span>
                </div>
                <div class="field">
                    <span class="label">Sucursal:</span>
                    <span class="value">{{ $clinicalRecord->branch->name ?? 'N/A' }}</span>
                </div>
                <div class="field">
                    <span class="label">Tipo Consulta:</span>
                    <span class="value">{{ $clinicalRecord->consultation_type_label }}</span>
                </div>
            </div>
        </div>

        <!-- ============================================ -->
        <!-- ANTECEDENTES Y ALERGIAS                     -->
        <!-- ============================================ -->
        @if($clinicalRecord->reason_for_consultation)
        <div class="section">
            <div class="section-title">Motivo de Consulta</div>
            <div class="text-content">{{ $clinicalRecord->reason_for_consultation }}</div>
        </div>
        @endif

        <!-- ============================================ -->
        <!-- ANAMNESIS                                   -->
        <!-- ============================================ -->
        @if($clinicalRecord->anamnesis)
        <div class="section">
            <div class="section-title">Anamnesis</div>
            <div class="text-content">{{ $clinicalRecord->anamnesis }}</div>
        </div>
        @endif

        <!-- ============================================ -->
        <!-- SIGNOS VITALES Y MEDIDAS CORPORALES         -->
        <!-- ============================================ -->
        @if($clinicalRecord->vital_signs && array_filter($clinicalRecord->vital_signs))
        <div class="section">
            <div class="section-title">Signos Vitales y Medidas Corporales</div>
            <table class="vital-signs-table">
                <tr>
                    <td class="label-cell">Presión sanguínea:</td>
                    <td class="value-cell">
                        @if($clinicalRecord->vital_signs['blood_pressure_systolic'] && $clinicalRecord->vital_signs['blood_pressure_diastolic'])
                            {{ $clinicalRecord->vital_signs['blood_pressure_systolic'] }}/{{ $clinicalRecord->vital_signs['blood_pressure_diastolic'] }} mmHg
                        @else
                            —
                        @endif
                    </td>
                    <td class="label-cell">SO:</td>
                    <td class="value-cell">{{ $clinicalRecord->vital_signs['oxygen_saturation'] ?? '—' }} %</td>
                </tr>
                <tr>
                    <td class="label-cell">Frecuencia cardíaca:</td>
                    <td class="value-cell">{{ $clinicalRecord->vital_signs['heart_rate'] ?? '—' }} lpm</td>
                    <td class="label-cell">Temp:</td>
                    <td class="value-cell">{{ $clinicalRecord->vital_signs['temperature'] ?? '—' }} °C</td>
                </tr>
                <tr>
                    <td class="label-cell">Frecuencia respiratoria:</td>
                    <td class="value-cell">{{ $clinicalRecord->vital_signs['respiratory_rate'] ?? '—' }} rpm</td>
                    <td class="label-cell">Peso:</td>
                    <td class="value-cell">{{ $clinicalRecord->vital_signs['weight'] ?? '—' }} kg</td>
                </tr>
                <tr>
                    <td class="label-cell">Talla:</td>
                    <td class="value-cell">{{ $clinicalRecord->vital_signs['height'] ?? '—' }} cm</td>
                    <td class="label-cell">IMC:</td>
                    <td class="value-cell">{{ $clinicalRecord->vital_signs['bmi'] ?? '—' }}</td>
                </tr>
                @if($clinicalRecord->vital_signs['fetal_heart_rate'] || $clinicalRecord->vital_signs['uterine_height'] || $clinicalRecord->vital_signs['edema'] || $clinicalRecord->vital_signs['fetal_movements'])
                <tr>
                    <td class="label-cell">FC Fetal:</td>
                    <td class="value-cell">{{ $clinicalRecord->vital_signs['fetal_heart_rate'] ?? '—' }} lpm</td>
                    <td class="label-cell">Altura Uterina:</td>
                    <td class="value-cell">{{ $clinicalRecord->vital_signs['uterine_height'] ?? '—' }} cm</td>
                </tr>
                <tr>
                    <td class="label-cell">Edema:</td>
                    <td class="value-cell">{{ $clinicalRecord->vital_signs['edema'] ?? '—' }}</td>
                    <td class="label-cell">Mov. Fetales:</td>
                    <td class="value-cell">{{ $clinicalRecord->vital_signs['fetal_movements'] ?? '—' }}</td>
                </tr>
                @endif
            </table>
        </div>
        @endif

        <!-- ============================================ -->
        <!-- EXPLORACIÓN FÍSICA                          -->
        <!-- ============================================ -->
        @if($clinicalRecord->physical_exam)
        <div class="section">
            <div class="section-title">Exploración Física</div>
            <div class="text-content">{!! nl2br(e($clinicalRecord->physical_exam)) !!}</div>
        </div>
        @endif

        <!-- ============================================ -->
        <!-- DIAGNÓSTICO PRESUNTIVO                      -->
        <!-- ============================================ -->
        @if($clinicalRecord->presumptive_diagnosis)
        <div class="section">
            <div class="section-title">Diagnóstico Presuntivo</div>
            <div class="text-content">{{ $clinicalRecord->presumptive_diagnosis }}</div>
        </div>
        @endif

        <!-- ============================================ -->
        <!-- TRATAMIENTO                                 -->
        <!-- ============================================ -->
        @if($clinicalRecord->treatment)
        <div class="section">
            <div class="section-title">Tratamiento</div>
            <div class="text-content">{!! nl2br(e($clinicalRecord->treatment)) !!}</div>
        </div>
        @endif

        <!-- ============================================ -->
        <!-- EVOLUCIÓN                                   -->
        <!-- ============================================ -->
        @if($clinicalRecord->evolution)
        <div class="section">
            <div class="section-title">Evolución</div>
            <div class="text-content">{{ $clinicalRecord->evolution }}</div>
        </div>
        @endif

        <!-- ============================================ -->
        <!-- OBSERVACIONES                               -->
        <!-- ============================================ -->
        @if($clinicalRecord->observations)
        <div class="section">
            <div class="section-title">Observaciones</div>
            <div class="text-content">{{ $clinicalRecord->observations }}</div>
        </div>
        @endif

        <!-- ============================================ -->
        <!-- RECOMENDACIONES                             -->
        <!-- ============================================ -->
        @if($clinicalRecord->recommendations)
        <div class="section">
            <div class="section-title">Recomendaciones</div>
            <div class="text-content">{{ $clinicalRecord->recommendations }}</div>
        </div>
        @endif

        <!-- ============================================ -->
        <!-- DOCUMENTOS ADJUNTOS (opcional)              -->
        <!-- ============================================ -->
        @if($clinicalRecord->documents->count() > 0)
        <div class="section">
            <div class="section-title">Documentos Adjuntos</div>
            <div class="text-content">
                <ul>
                    @foreach($clinicalRecord->documents as $doc)
                        <li>{{ $doc->name }} ({{ $doc->file_name }})</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        <!-- ============================================ -->
        <!-- FOOTER                                      -->
        <!-- ============================================ -->
        <div class="footer">
            <div class="footer-text">{{ $company['footer_text'] ?? 'Gracias por confiar en nosotros' }}</div>
            <div class="footer-company">
                {{ $company['business_name'] ?? $company['name'] ?? 'SAP PROSAUD' }}
                @if($company['rnc'])
                    · RNC: {{ $company['rnc'] }}
                @endif
            </div>
            <div style="font-size:9px;margin-top:3px;color:#666;">
                Historia Clínica generada el {{ now()->format('d/m/Y H:i') }}
            </div>
        </div>

    </div>
</body>
</html>