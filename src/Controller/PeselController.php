<?php

namespace App\Controller;

use App\Form\PeselType;
use App\Validator\Pesel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PeselController extends AbstractController
{
    #[Route('/', name: 'pesel_form')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(PeselType::class);
        $form->handleRequest($request);

        return $this->renderForm('pesel/index.html.twig', [
            'form' => $form,
            'valid' => $form->isSubmitted() && $form->isValid(),
        ]);
    }

    #[Route('/api/pesel', name: 'api_pesel', methods: ['POST'])]
    public function api(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $pesel = $data['pesel'] ?? null;

        if(!is_string($pesel)) {
            return $this->json(['error' => 'Brak pola "pesel" w treści żądania.'], Response::HTTP_BAD_REQUEST);
        }

        $errors = [];
        foreach ($validator->validate($pesel, [new NotBlank(), new Pesel()]) as $violation) {
            $errors[] = $violation->getMessage();
        }

        return $this->json([
            'pesel' => $pesel,
            'valid' => count($errors) === 0,
            'errors' => $errors,
        ]);
    }
}
