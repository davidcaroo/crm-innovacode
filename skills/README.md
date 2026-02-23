# Skills para Claude - CRM PHP + Bootstrap

Esta carpeta contiene skills personalizados para Claude que enseñan mejores prácticas de desarrollo basadas en este proyecto CRM.

## Skills Disponibles

### 📘 php-bootstrap-crm-developer

**Especialidad**: Desarrollo frontend con PHP procedural, Bootstrap 4, PDO y Chart.js

**Úsalo para**:
- Crear nuevas funcionalidades CRUD
- Implementar dashboards con visualización de datos
- Seguir las convenciones del proyecto
- Aplicar patrones de seguridad (PDO, sanitización, validación)
- Integrar componentes Bootstrap correctamente
- Crear gráficos con Chart.js

**Ejemplos de uso con Claude**:
```
🤖 "Usando el skill php-bootstrap-crm-developer, crea un módulo de productos con CRUD completo"

🤖 "Genera un dashboard de ventas con gráficos de barras siguiendo los patrones del skill"

🤖 "Crea un formulario de registro de usuarios con validación según el skill"
```

## Cómo Usar los Skills

### En Claude.ai
1. Sube el archivo SKILL.md del skill que quieras usar
2. Claude automáticamente cargará las instrucciones
3. Menciona el skill en tu solicitud para activarlo

### En Claude Code
1. Instala como plugin local siguiendo la documentación de Claude Code
2. Activa el skill mencionándolo en la conversación

### Vía API
```python
# Ejemplo con la API de Claude
import anthropic

client = anthropic.Anthropic(api_key="tu-api-key")

with open("skills/php-bootstrap-crm-developer/SKILL.md", "r", encoding="utf-8") as f:
    skill_content = f.read()

message = client.messages.create(
    model="claude-3-5-sonnet-20241022",
    max_tokens=4096,
    messages=[
        {
            "role": "user", 
            "content": f"{skill_content}\n\nCrea un módulo de inventario con CRUD completo"
        }
    ]
)
```

## Estructura de un Skill

Cada skill sigue el estándar de Agent Skills:

```markdown
---
name: nombre-del-skill
description: Descripción completa de qué hace el skill
---

# Título del Skill

[Instrucciones detalladas que Claude seguirá]

## Secciones recomendadas
- Patrones de código
- Ejemplos completos
- Mejores prácticas
- Troubleshooting
- Checklist
```

## Crear Tu Propio Skill

1. Crea una nueva carpeta en `skills/tu-skill-name/`
2. Crea un archivo `SKILL.md` con frontmatter YAML
3. Incluye instrucciones claras y ejemplos
4. Documenta convenciones específicas de tu proyecto
5. Agrega casos de uso y mejores prácticas

**Plantilla básica**:
```markdown
---
name: tu-skill-name
description: Descripción de qué hace tu skill y cuándo usarlo
---

# Tu Skill Name

Instrucciones que Claude seguirá cuando use este skill.

## Patrones
- Patrón 1
- Patrón 2

## Ejemplos
\`\`\`php
// Tu código de ejemplo
\`\`\`

## Guidelines
- Guideline 1
- Guideline 2
```

## Beneficios de los Skills

✅ **Consistencia**: Todos siguen los mismos patrones  
✅ **Velocidad**: Claude conoce tus convenciones  
✅ **Calidad**: Aplica mejores prácticas automáticamente  
✅ **Documentación**: El skill es documentación viva  
✅ **Onboarding**: Nuevos desarrolladores aprenden rápido  

## Referencias

- [Agent Skills Standard](https://agentskills.io)
- [Anthropic Skills Repository](https://github.com/anthropics/skills)
- [Documentación de Claude](https://docs.anthropic.com)

---

**Notas**:
- Los skills son específicos para este proyecto CRM
- Mantén los skills actualizados conforme evoluciona el proyecto
- Documenta nuevos patrones que emerjan
