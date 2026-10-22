<?php
declare(strict_types=1);
header('Content-Type: text/html; charset=UTF-8');
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Scanner Web → Perfil Mobile</title>
<style>
:root{--p:#153e75;--bg:#f4f6f8;--card:#fff;--muted:#64748b}
*{box-sizing:border-box}body{margin:0;font-family:Arial,sans-serif;background:var(--bg);color:#1f2937}
.wrap{max-width:760px;margin:auto;padding:18px}.card{background:var(--card);border:1px solid #dbe3ec;border-radius:16px;padding:18px;margin-bottom:14px}
h1{font-size:24px;margin:0 0 8px}h2{font-size:18px;margin-top:0}.muted{color:var(--muted)}
label{display:block;font-weight:700;margin:10px 0}input{width:100%;padding:12px;border:1px solid #cbd5e1;border-radius:10px;font-size:16px}
button{border:0;border-radius:11px;background:var(--p);color:#fff;padding:13px 16px;font-weight:700;font-size:16px;cursor:pointer}
button.secondary{background:#475569}.actions{display:flex;gap:10px;flex-wrap:wrap}
.status{white-space:pre-wrap;background:#eef6ff;border:1px solid #bfdbfe;border-radius:10px;padding:12px;margin-top:12px}
.warn{background:#fff7ed;border-color:#fed7aa}.ok{background:#ecfdf5;border-color:#a7f3d0}
table{width:100%;border-collapse:collapse}td,th{text-align:left;border-bottom:1px solid #e5e7eb;padding:8px;font-size:13px}
</style>
</head>
<body>
<div class="wrap">
  <div class="card">
    <h1>Exportar configuración para Scanner Mobile</h1>
    <p class="muted">Addon compatible con Scanner Web 0.6.36. Lee las pantallas de configuración ya instaladas en este mismo Scanner y genera un JSON portable.</p>
    <label>Empresa / base Profit
      <input id="companyCode" autocomplete="off" autocapitalize="characters" placeholder="Ej. VIMAR">
    </label>
    <label>Nombre de empresa
      <input id="companyName" autocomplete="off" placeholder="Ej. Vimar">
    </label>
    <div class="actions">
      <button id="exportBtn" type="button">Generar JSON para APK</button>
      <button id="inspectBtn" type="button" class="secondary">Analizar configuración</button>
    </div>
    <div id="status" class="status">Listo. Debe abrir esta página dentro de la misma instalación Scanner Web y con la sesión activa.</div>
  </div>
  <div class="card">
    <h2>Qué viaja</h2>
    <p>Módulos, colores, fuentes de códigos, reglas/presentaciones visibles, entrada de conteo, cámara/formatos, filtros Profit y demás parámetros no sensibles encontrados.</p>
    <p><strong>No viaja:</strong> contraseña/usuario SQL, tokens, secretos, DSN, servidor/base SQL, rutas locales/UNC, carpetas de intercambio ni URL del Bridge.</p>
  </div>
  <div class="card">
    <h2>Secciones detectadas</h2>
    <table><thead><tr><th>Sección</th><th>Endpoint</th><th>Campos</th><th>Estado</th></tr></thead><tbody id="resultBody"></tbody></table>
  </div>
</div>
<script>
(() => {
  'use strict';

  const PROFILE_SCHEMA='scanner.config.profile';
  const PROFILE_FORMAT=1;
  const WEB_VERSION='0.6.36';
  const endpoints=[
    ['general','setup.php'],
    ['codes','barcode_sources.php'],
    ['presentations','presentation_rules.php'],
    ['input','input_settings.php'],
    ['camera','camera_settings.php'],
    ['images','images.php'],
    ['filters','profit_filters.php'],
    ['outputs','output_settings.php']
  ];

  const secretRx=/(password|passwd|pwd|secret|token|authorization|csrf|sql_user|sqluser|db_user|dbuser)/i;
  const localRx=/(root_path|fallback_path|unc|directory|folder|carpeta|exchange|outbox|pending|processed|error_dir|bridge_url|server|database|dsn)/i;

  function text(el){return (el?.textContent||'').replace(/\s+/g,' ').trim();}
  function labelFor(doc,el){
    if(el.id){
      const l=doc.querySelector('label[for="'+CSS.escape(el.id)+'"]');
      if(l)return text(l);
    }
    const parent=el.closest('label');
    return parent?text(parent):'';
  }
  function keyOf(el){return (el.getAttribute('name')||el.id||'').trim();}
  function safeField(key){return key && !secretRx.test(key) && !localRx.test(key);}
  function valueOf(el){
    if(el instanceof HTMLInputElement){
      if(['checkbox','radio'].includes(el.type))return el.checked;
      if(['password','file','hidden','submit','button'].includes(el.type))return undefined;
      return el.value;
    }
    if(el instanceof HTMLSelectElement){
      if(el.multiple)return [...el.selectedOptions].map(o=>o.value);
      return el.value;
    }
    if(el instanceof HTMLTextAreaElement)return el.value;
    return undefined;
  }

  function serializePage(html,url){
    const doc=new DOMParser().parseFromString(html,'text/html');
    const fields={};
    const labels={};
    const excluded=[];
    doc.querySelectorAll('input,select,textarea').forEach(el=>{
      const key=keyOf(el);
      if(!key)return;
      const value=valueOf(el);
      if(value===undefined)return;
      if(!safeField(key)){excluded.push(key);return;}
      fields[key]=value;
      const label=labelFor(doc,el);
      if(label)labels[key]=label;
    });
    return {url,fields,labels,excluded};
  }

  function normalizeTheme(section){
    const out={};
    if(!section)return out;
    for(const [key,value] of Object.entries(section.fields||{})){
      if(typeof value!=='string'||!/^#[0-9a-f]{6}$/i.test(value))continue;
      const label=(section.labels?.[key]||key).toLowerCase();
      if(label.includes('principal'))out.primary=value;
      else if(label.includes('secund'))out.secondary=value;
      else if(label.includes('acento'))out.accent=value;
      else if(label.includes('fondo'))out.background=value;
      else if(label.includes('tarjeta'))out.cards=value;
    }
    return out;
  }

  function normalizeModules(section){
    const out={};
    if(!section)return out;
    const map=[
      [/picking|despacho/i,'picking'],
      [/inventario/i,'inventory'],
      [/recepci[oó]n de producci[oó]n/i,'production_receiving'],
      [/recepci[oó]n/i,'receiving'],
      [/cotizar|facturar|venta desde/i,'scan_sale'],
      [/traslado/i,'transfer'],
      [/bulto|despacho f[ií]sico/i,'packages'],
      [/consulta.*producto|producto/i,'product_lookup'],
      [/historial/i,'history']
    ];
    for(const [key,value] of Object.entries(section.fields||{})){
      if(typeof value!=='boolean')continue;
      const label=(section.labels?.[key]||key);
      const pair=map.find(([rx])=>rx.test(label));
      if(pair)out[pair[1]]=value;
    }
    return out;
  }

  async function collect(){
    const sections={};
    const results=[];
    for(const [id,url] of endpoints){
      try{
        const res=await fetch(url,{credentials:'same-origin',cache:'no-store'});
        if(!res.ok){results.push({id,url,count:0,status:'HTTP '+res.status});continue;}
        const html=await res.text();
        if(/<form[^>]+login|name=["']password["']/i.test(html) && /iniciar sesi[oó]n|ingresar/i.test(html)){
          results.push({id,url,count:0,status:'requiere sesión'});continue;
        }
        const section=serializePage(html,url);
        sections[id]=section;
        results.push({id,url,count:Object.keys(section.fields).length,status:'OK'});
      }catch(err){
        results.push({id,url,count:0,status:String(err)});
      }
    }
    return {sections,results};
  }

  function renderResults(results){
    const body=document.getElementById('resultBody');
    body.innerHTML=results.map(r=>'<tr><td>'+r.id+'</td><td>'+r.url+'</td><td>'+r.count+'</td><td>'+r.status+'</td></tr>').join('');
  }

  async function buildProfile(download){
    const company=(document.getElementById('companyCode').value||'').trim().toUpperCase();
    const companyName=(document.getElementById('companyName').value||company).trim();
    if(!company)throw new Error('Indique la empresa/base Profit.');
    const status=document.getElementById('status');
    status.className='status';
    status.textContent='Analizando pantallas de configuración...';

    const {sections,results}=await collect();
    renderResults(results);

    const profile={
      schema:PROFILE_SCHEMA,
      format_version:PROFILE_FORMAT,
      source:{
        product:'Scanner Web',
        version:WEB_VERSION,
        addon:'CFGPORT1',
        exported_at:new Date().toISOString()
      },
      company:{code:company,name:companyName},
      portable:{
        theme:normalizeTheme(sections.general),
        modules:normalizeModules(sections.general),
        sections
      },
      web_snapshot:sections,
      excluded:[
        'credenciales SQL Server',
        'password/token/secret/authorization',
        'servidor/base/DSN SQL',
        'rutas locales o UNC de imágenes',
        'carpetas OUTBOX/PENDING/PROCESSED/ERROR',
        'URL Scanner Bridge'
      ]
    };

    const ok=results.filter(x=>x.status==='OK').length;
    status.className='status ok';
    status.textContent='Perfil preparado. Secciones leídas: '+ok+'/'+results.length+'.\nEmpresa: '+company+'\nWeb: '+WEB_VERSION+'\nNo se exportaron secretos ni rutas locales.';

    if(download){
      const json=JSON.stringify(profile,null,2);
      const blob=new Blob([json],{type:'application/json;charset=utf-8'});
      const url=URL.createObjectURL(blob);
      const a=document.createElement('a');
      a.href=url;
      a.download='SCANNER_WEB_PROFILE_'+company+'_WEB'+WEB_VERSION+'.json';
      document.body.appendChild(a);a.click();a.remove();
      setTimeout(()=>URL.revokeObjectURL(url),500);
    }
    return profile;
  }

  document.getElementById('inspectBtn').addEventListener('click',()=>buildProfile(false).catch(err=>{
    const s=document.getElementById('status');s.className='status warn';s.textContent=String(err?.message||err);
  }));
  document.getElementById('exportBtn').addEventListener('click',()=>buildProfile(true).catch(err=>{
    const s=document.getElementById('status');s.className='status warn';s.textContent=String(err?.message||err);
  }));
})();
</script>
</body>
</html>
