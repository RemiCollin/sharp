<?php

namespace Code16\Sharp\Http\Api;

class FormController extends ApiController
{

    /**
     * @param string $entityKey
     * @param string $instanceId
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($entityKey, $instanceId)
    {
        sharp_check_ability("view", $entityKey, $instanceId);

        $form = $this->getFormInstance($entityKey);

        return response()->json([
            "fields" => $form->fields(),
            "layout" => $form->formLayout(),
            "data" => $form->instance($instanceId)
        ]);
    }

    /**
     * @param string $entityKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function create($entityKey)
    {
        sharp_check_ability("create", $entityKey);

        $form = $this->getFormInstance($entityKey);

        return response()->json([
            "fields" => $form->fields(),
            "layout" => $form->formLayout(),
            "data" => $form->newInstance()
        ]);
    }

    /**
     * @param string $entityKey
     * @param string $instanceId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($entityKey, $instanceId)
    {
        sharp_check_ability("update", $entityKey, $instanceId);

        $this->validateRequest($entityKey);

        $form = $this->getFormInstance($entityKey);

        $form->updateInstance($instanceId, request()->all());

        return response()->json(["ok" => true]);
    }

    /**
     * @param string $entityKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function store($entityKey)
    {
        sharp_check_ability("create", $entityKey);

        $this->validateRequest($entityKey);

        $form = $this->getFormInstance($entityKey);

        $form->storeInstance(request()->all());

        return response()->json(["ok" => true]);
    }

    /**
     * @param string $entityKey
     * @param string $instanceId
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($entityKey, $instanceId)
    {
        sharp_check_ability("delete", $entityKey, $instanceId);

        $form = $this->getFormInstance($entityKey);

        $form->delete($instanceId);

        return response()->json(["ok" => true]);
    }

    /**
     * @param string $entityKey
     */
    protected function validateRequest(string $entityKey)
    {
        $validatorClass = config("sharp.entities.{$entityKey}.validator");

        if(class_exists($validatorClass)) {
            // Validation is automatically called (FormRequest)
            app($validatorClass);
        }
    }
}