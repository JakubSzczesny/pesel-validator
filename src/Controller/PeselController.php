<?php

namespace App\Controller;

use App\Form\PeselType;
use App\Service\PeselDecoder;
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
    public function __construct(private PeselDecoder $decoder)
    {
    }

    #[Route('/', name: 'pesel_form')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(PeselType::class);
        $form->handleRequest($request);

        $birthDate = null;
        $gender = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $pesel = $form->get('pesel')->getData();
            $birthDate = $this->decoder->getBirthDate($pesel);
            $gender = $this->decoder->getGender($pesel);
        }

        return $this->renderForm('pesel/index.html.twig', [
            'form' => $form,
            'birthDate' => $birthDate,
            'gender' => $gender,
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

        if (count($errors) > 0) {
            return $this->json(['pesel' => $pesel, 'valid' => false, 'errors' => $errors]);
        }

        return $this->json([
            'pesel' => $pesel,
            'valid' => true,
            'birthDate' => $this->decoder->getBirthDate($pesel)->format('Y-m-d'),
            'gender' => $this->decoder->getGender($pesel),
        ]);
    }
}
