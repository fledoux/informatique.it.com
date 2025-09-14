<?php

# console make:crud-bootstrap User

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(
  name: 'make:crud-bootstrap',
  description: 'Génère un CRUD avec des templates Bootstrapés pour une entité donnée',
)]
class CrudBootstrapCommand extends Command
{
  protected function configure(): void
  {
    $this
      ->addArgument('entity', InputArgument::REQUIRED, 'Nom de l\'entité (ex : Product)');
  }

  protected function execute(InputInterface $input, OutputInterface $output): int
  {
    $io = new SymfonyStyle($input, $output);
    $entity = $input->getArgument('entity');

    $entityClass = 'App\\Entity\\' . $entity;
    $entityVar = lcfirst($entity);
    $entityRoute = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $entity));
    $templateDir = 'templates/' . $entityRoute;

    $fs = new Filesystem();

    if (!class_exists($entityClass)) {
      $io->error("L'entité $entityClass n'existe pas.");
      return Command::FAILURE;
    }

    // Crée le dossier templates si besoin
    $fs->mkdir($templateDir);

    // Liste les propriétés de l'entité
    $fields = $this->getEntityFields($entityClass);

    $theadHtml = '';
    $tbodyHtml = '';
    $dlHtml = '';

    foreach ($fields as $field) {
      $theadHtml .= "      <th class=\"text-left\">{{ '" . $entity . "." . ucfirst($field) . "'|trans|raw }}</th>\n";
      $tbodyHtml .= "        <td class=\"text-left\">{{ item.$field }}</td>\n";
      $dlHtml .= "  <dt class=\"col-sm-3\">" . ucfirst($field) . "</dt>\n";
      $dlHtml .= "  <dd class=\"col-sm-9\">{{ {$entityVar}.$field }}</dd>\n";
    }

    $templates = [
      'index' => <<<TWIG
{% extends 'base.html.twig' %}
{% block title %}{{ '$entity.List'|trans|raw }}{% endblock %}
{% block body %}
<h1 class="mb-4">{{ '$entity.List'|trans|raw }}</h1>
<div class="table-responsive-lg">
<table class="table align-middle table-sm table-bordered table-hover">
  <thead>
    <tr>
$theadHtml
<th>{{ 'Global.Actions'|trans|raw }}</th>
    </tr>
  </thead>
  <tbody>
    {% for item in ${entityVar}s %}
      <tr>
$tbodyHtml
        <td>
          <a href="{{ path('app_{$entityRoute}_show', {'id': item.id}) }}" class="btn btn-link text-decoration-none text-primary">{{ 'Btn.View'|trans|raw }}</a>
          <a href="{{ path('app_{$entityRoute}_edit', {'id': item.id}) }}" class="btn btn-link text-decoration-none text-primary">{{ 'Btn.Edit'|trans|raw }}</a>
          {% include '{$entityRoute}/_delete_form.html.twig' with { '{$entityRoute}': item } %}
        </td>
      </tr>
    {% endfor %}
  </tbody>
</table>
</div>
<a href="{{ path('app_{$entityRoute}_new') }}" class="btn btn-orange mt-3">{{ 'Btn.New'|trans|raw }}</a>
{% endblock %}
TWIG,

      '_form' => <<<TWIG
{{ form_start(form) }}
  {{ form_widget(form) }}
   <div class="btn-group mt-3" role="group" aria-label="Basic example">
<button class="btn btn-danger">{{ 'Btn.Save'|trans|raw }}</button>
<a href="{{ path('app_{$entityRoute}_index') }}" class="btn btn-outline-danger">{{ 'Btn.Back'|trans|raw }}</a>
</div>
{{ form_end(form) }}
TWIG,

      'new' => <<<TWIG
{% extends 'base.html.twig' %}
{% block title %}{{ '$entity.Add'|trans|raw }}{% endblock %}
{% block body %}
<h1>{{ '$entity.Add'|trans|raw }}</h1>
{{ include('$entityRoute/_form.html.twig') }}
{% endblock %}
TWIG,

      'edit' => <<<TWIG
{% extends 'base.html.twig' %}
{% block title %}{{ '$entity.Update'|trans|raw }}{% endblock %}
{% block body %}
<h1>{{ '$entity.Update'|trans|raw }}</h1>
{{ include('$entityRoute/_form.html.twig') }}
{% endblock %}
TWIG,

      'show' => <<<TWIG
{% extends 'base.html.twig' %}
{% block title %}{{ '$entity.View'|trans|raw }}{% endblock %}
{% block body %}
<h1>{{ '$entity.View'|trans|raw }}</h1>
<dl class="row">
$dlHtml</dl>
<div class="btn-group" role="group" aria-label="Basic example">
<a href="{{ path('app_{$entityRoute}_edit', {'id': $entityVar.id}) }}" class="btn btn-orange">Modifier</a>
<a href="{{ path('app_{$entityRoute}_index') }}" class="btn btn-outline-orange">Retour</a>
</div>
{% endblock %}
TWIG,

      '_delete_form' => <<<TWIG
<form method="post"
      action="{{ path('app_{$entityRoute}_delete', {'id': {$entityVar}|raw.id}) }}"
      onsubmit="return confirm('{{ 'Are you sure you want to delete this item'|trans|raw }}');"
      style="display:inline">
  <input type="hidden" name="_token" value="{{ csrf_token('delete' ~ {$entityVar}|raw.id) }}">
  <button class="btn btn-link text-decoration-none text-danger p-0">
    {{ 'Btn.Delete'|trans|raw }}
  </button>
</form>
TWIG,
    ];

    foreach ($templates as $name => $content) {
      $fs->dumpFile("$templateDir/$name.html.twig", $content);
    }

    $io->success("Templates Bootstrap pour l'entité '$entity' générés dans $templateDir.");

    return Command::SUCCESS;
  }

  private function getEntityFields(string $entityClass): array
  {
    $reflection = new \ReflectionClass($entityClass);
    $fields = [];

    foreach ($reflection->getProperties(\ReflectionProperty::IS_PRIVATE) as $property) {
      $fields[] = $property->getName();
    }

    return $fields;
  }
}
