<?php

use PHPUnit\Framework\TestCase;
use src\controllers\UserController;

class UserControllerTest extends TestCase {
    private $userController;

    protected function setUp(): void {
        $this->userController = new UserController();
    }

    public function testGetUsers() {
        $users = $this->userController->getUsers();
        $this->assertIsArray($users);
    }

    public function testUpdateUserWithMissingData() {
        $data = ['id' => 1, 'name' => '', 'email' => ''];
        $response = $this->userController->updateUser($data);
        $this->assertEquals(400, http_response_code());
        $this->assertEquals(['message' => 'ID, nome e email sao obrigatorios'], $response);
    }

    public function testUpdateUserWithValidData() {
        $data = ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com'];
        $response = $this->userController->updateUser($data);
        $this->assertEquals(['message' => 'Usuario atualizado com sucesso'], $response);
    }

    public function testDeleteUserWithMissingId() {
        $data = ['id' => ''];
        $response = $this->userController->deleteUser($data);
        $this->assertEquals(400, http_response_code());
        $this->assertEquals(['message' => 'ID e obrigatorio'], $response);
    }

    public function testDeleteUserWithValidId() {
        $data = ['id' => 1];
        $response = $this->userController->deleteUser($data);
        $this->assertEquals(['message' => 'Usuario deletado com sucesso'], $response);
    }
}