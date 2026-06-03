Explication des exceptions utilisés:

Http (Vérification):
- Localisation: Controllers 
- Cas d'usage: Si Demande utilisateur OK (ex: Formulaire NOK, CSRF NOK)
- Try/Catch: HttpExceptionInterface (ValidationException, CsrfException)
- Try/Catch (venant d'en bas): ApplicationExceptionInterface

Application (Fonctionnalité):
- Localisation: UseCase 
- Cas d'usage: Fonctionnalités applicative OK (Ex: RateLimit NOK, Mail NOK)
- Throw new: ApplicationExceptionInterface
- Try/Catch (venant d'en bas): DomainExceptionInterface

Domain (Intégrité):
- Localisation: Service, Repository
- Case d'usage: Intégrité des données OK (Ex: clés unique NOK, Entitée trouvée NOK)
- Throw new: DomainExceptionInterface
- Try/Catch: PDOException