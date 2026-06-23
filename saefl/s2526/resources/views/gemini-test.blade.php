<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gemini API Test</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            padding: 30px;
        }
        h1 {
            color: #333;
            margin-bottom: 30px;
            text-align: center;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: bold;
        }
        textarea, select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        textarea:focus, select:focus {
            outline: none;
            border-color: #667eea;
        }
        textarea {
            min-height: 120px;
            resize: vertical;
        }
        button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: transform 0.2s;
        }
        button:hover {
            transform: translateY(-2px);
        }
        button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .response {
            margin-top: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #667eea;
            display: none;
        }
        .response.show {
            display: block;
        }
        .response h3 {
            color: #667eea;
            margin-bottom: 15px;
        }
        .response-text {
            color: #333;
            line-height: 1.6;
            white-space: pre-wrap;
        }
        .loading {
            text-align: center;
            color: #667eea;
            font-weight: bold;
            display: none;
        }
        .loading.show {
            display: block;
        }
        .error {
            background: #fee;
            border-left-color: #f44336;
        }
        .error h3 {
            color: #f44336;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🤖 Gemini API - Panel de Pruebas</h1>
        
        <form id="geminiForm">
            <div class="form-group">
                <label for="model">Modelo:</label>
                <select id="model" name="model">
                    <option value="gemini-2.5-flash">Gemini 2.5 Flash (Recomendado - Rápido y Versátil)</option>
                    <option value="gemini-2.5-pro">Gemini 2.5 Pro (Más Avanzado - Tareas Complejas)</option>
                    <option value="gemini-2.0-flash">Gemini 2.0 Flash (Multimodal Versátil)</option>
                    <option value="gemini-2.5-flash-lite">Gemini 2.5 Flash-Lite (Ultra Rápido)</option>
                    <option value="gemini-2.0-flash-lite">Gemini 2.0 Flash-Lite (Ligero)</option>
                </select>
            </div>

            <div class="form-group">
                <label for="prompt">Tu pregunta o prompt:</label>
                <textarea id="prompt" name="prompt" placeholder="Escribe tu pregunta aquí..."></textarea>
            </div>

            <button type="submit">Enviar a Gemini</button>
        </form>

        <div class="loading" id="loading">
            ⏳ Procesando tu solicitud...
        </div>

        <div class="response" id="response">
            <h3>Respuesta de Gemini:</h3>
            <div class="response-text" id="responseText"></div>
        </div>
    </div>

    <script>
		document.getElementById('geminiForm').addEventListener('submit', async (e) => {
		  e.preventDefault();

		  const prompt = document.getElementById('prompt').value;
		  const model  = document.getElementById('model').value;

		  const loading  = document.getElementById('loading');
		  const response = document.getElementById('response');
		  const out      = document.getElementById('responseText');
		  const submitBtn = e.target.querySelector('button');

		  if (!prompt.trim()) {
		    response.classList.add('show', 'error');
		    out.textContent = 'Por favor escribe un prompt';
		    return;
		  }

		  response.classList.remove('error');
		  response.classList.add('show');
		  out.textContent = '';
		  loading.classList.add('show');
		  submitBtn.disabled = true;

		  try {
		    const res = await fetch('/api/gemini/stream', {
		      method: 'POST',
		      headers: {
		        'Content-Type': 'application/json',
		        'Accept': 'text/event-stream',
		        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
		      },
		      body: JSON.stringify({ prompt, model })
		    });

		    if (!res.ok || !res.body) {
		      const txt = await res.text();
		      throw new Error('Error HTTP ' + res.status + ': ' + txt.slice(0, 200));
		    }

		    const reader = res.body.getReader();
		    const decoder = new TextDecoder('utf-8');

		    let buffer = '';

		    while (true) {
		      const { value, done } = await reader.read();
		      if (done) break;

		      buffer += decoder.decode(value, { stream: true });

		      // SSE event separator: \n\n
		      let idx;
		      while ((idx = buffer.indexOf('\n\n')) !== -1) {
		        const rawEvent = buffer.slice(0, idx);
		        buffer = buffer.slice(idx + 2);

		        // Parse simple SSE: event: X / data: Y
		        const lines = rawEvent.split('\n').map(l => l.trim());
		        let eventName = 'message';
		        let dataLine = null;

		        for (const line of lines) {
		          if (line.startsWith('event:')) eventName = line.slice(6).trim();
		          if (line.startsWith('data:')) dataLine = line.slice(5).trim();
		        }

		        if (!dataLine) continue;

		        if (eventName === 'token') {
		          const payload = JSON.parse(dataLine);
		          out.textContent += payload.text;
		        } else if (eventName === 'error') {
		          const payload = JSON.parse(dataLine);
		          response.classList.add('error');
		          out.textContent += '\n\n[ERROR] ' + payload.message;
		        } else if (eventName === 'done') {
		          // fin
		        }
		      }
		    }

		  } catch (err) {
		    response.classList.add('error');
		    out.textContent = 'Error: ' + err.message;
		  } finally {
		    loading.classList.remove('show');
		    submitBtn.disabled = false;
		  }
		});
		</script>


</body>
</html>