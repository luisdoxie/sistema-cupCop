<?php

namespace App\Http\Controllers;

abstract class Controller
{
    protected function pdfLimitError(int $total, string $filtroSugerido): \Illuminate\Http\Response
    {
        $html = <<<HTML
<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8">
<title>PDF no disponible</title>
<style>
  body{font-family:Arial,sans-serif;background:#f9fafb;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0}
  .box{background:#fff;border-radius:8px;box-shadow:0 2px 12px rgba(0,0,0,.1);padding:32px 40px;max-width:480px;text-align:center}
  .icon{font-size:48px;margin-bottom:12px}
  h2{color:#dc2626;margin:0 0 8px}
  p{color:#6b7280;font-size:14px;line-height:1.6;margin:0 0 16px}
  .count{font-weight:bold;color:#111}
  .tip{background:#fef3c7;border:1px solid #fcd34d;border-radius:6px;padding:10px 14px;font-size:13px;color:#92400e;margin-bottom:20px}
  button{background:#1d4ed8;color:#fff;border:none;padding:10px 24px;border-radius:6px;cursor:pointer;font-size:14px}
  button:hover{background:#1e40af}
</style></head><body>
<div class="box">
  <div class="icon">⚠️</div>
  <h2>PDF no disponible</h2>
  <p>El reporte contiene <span class="count">{$total} registros</span>. El generador de PDF admite hasta <span class="count">500 registros</span>.</p>
  <div class="tip">Aplica un filtro de <strong>{$filtroSugerido}</strong> y vuelve a intentarlo.<br>Para el reporte completo sin límite usa <strong>Excel</strong>.</div>
  <button onclick="window.close()">Cerrar pestaña</button>
</div>
</body></html>
HTML;
        return response($html, 200, ['Content-Type' => 'text/html; charset=UTF-8']);
    }
}
