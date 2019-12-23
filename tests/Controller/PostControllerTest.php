<?php

namespace App\Tests\Controller;
use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Exception\ValidationException;

/**
 * NO CANVAS COPIAR AS DUAS PASTAS JUNTO COM O CONTEUDO E COLOCAR DENTRO DAS PASTA DO PROJETO 
 * BLOG > SOURCE > CRIAR PASTA ->event
 * 
 */

final class PostControllerTest extends WebTestCase
{   
    private EntityManagerInterface $em;
    private KernelBrowser $client;

    public function setUp(): void{
        $this->client   = self::createClient();
        $this->em       = self::$kernel->getContainer()->get('doctrine')->getManager();
        $tool = new SchemaTool($this->em);

        $metadata = $this->em->getClassMetadata(Post::class);
        $tool->dropSchema([$metadata]);
        try{
            // cria a tabela associada a entidade Post
            $tool->createSchema([$metadata]);        
        }catch(ToolsException $e){
            $this->fail("Impossivel criar tabela Post:". $e->getMessage());
        }
    }

    // Teste de inserção de registro 
    public function test_create_post(): void{
        $this->client->request('POST','/posts',[], [], [], json_encode([    
           'title'=>'postagem 1',
           'description' =>'Alguma descrição' 
        ]));
        $this->assertEquals(Response::HTTP_CREATED,$this->client->getResponse()->getStatusCode());
    }

    // Teste de remoçao de itens 
    public function test_delete_post(): void {
        $post = new Post('teste', 'Teste de remoção');
        $this->em->persist($post);
        $this->em->flush();
        $this->client->request('DELETE', '/posts/1');
        $this->assertEquals(Response::HTTP_NO_CONTENT, $this->client->getResponse()->getStatusCode());
    }
    
    // Listagem de post selecionado 
    public function test_list_post_select(): void {
        $post = new Post("Postagem", "Conteudo da postagems");
        $this->em->persist($post);
        $this->em->flush();
        
        $this->client->request('get','/posts/1', [], [], [], null);    
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    // Teste de título inválido
    public function test_create_post_with_invalid_title(): void {
        $this->client->request('POST','/posts',[],[],[], json_encode([
            'description'=>'algum txt'
        ]));

        $this->assertEquals(Response::HTTP_BAD_REQUEST,$this->client->getResponse()->getStatusCode());
    }


    public function test_list_all_post(): void{
        $post = new Post("post", "Conteúdo da postagem");
        $this->em->persist($post);
        $this->em->flush();
        $this->client->request('GET', '/posts', [], [], [], null);
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    // Alteração de postagem
    public function test_update_post(): void {
        $post = new Post("Postagem", "Conteudo da postagems");
        $this->em->persist($post);
        $this->em->flush();
        
        $this->client->request('PUT','/posts/1', [], [], [], json_encode([
            'title' => 'Alteração de postagem',
            'description' => 'Alteração de descrição'
            ]));    
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }
}
?>