from django.core.management.base import BaseCommand
from authentication.models import Users, MacraStaff
from django.utils import timezone

class Command(BaseCommand):
    help = 'Seed the database with 8 investigators and their skills.'

    def handle(self, *args, **kwargs):
        investigators = [
            {"username": "investigator1", "first_name": "Alice", "last_name": "Mwale", "skills": "Telecommunications, Broadcasting"},
            {"username": "investigator2", "first_name": "Brighton", "last_name": "Chirwa", "skills": "Broadcasting, Cybersecurity"},
            {"username": "investigator3", "first_name": "Caroline", "last_name": "Banda", "skills": "Telecommunications, Customer Service"},
            {"username": "investigator4", "first_name": "David", "last_name": "Phiri", "skills": "Contract Law, Service Quality"},
            {"username": "investigator5", "first_name": "Eunice", "last_name": "Kumwenda", "skills": "Billing, Customer Service"},
            {"username": "investigator6", "first_name": "Frank", "last_name": "Mvula", "skills": "Network Analysis, Service Quality"},
            {"username": "investigator7", "first_name": "Grace", "last_name": "Moyo", "skills": "Telecommunications, Billing"},
            {"username": "investigator8", "first_name": "Henry", "last_name": "Zimba", "skills": "Broadcasting, Contract Law"},
        ]
        for i, inv in enumerate(investigators, start=1):
            user, created = Users.objects.get_or_create(
                username=inv["username"],
                defaults={
                    "first_name": inv["first_name"],
                    "last_name": inv["last_name"],
                    "role": "macra_staff",
                    "is_staff": True,
                    "is_active": True,
                }
            )
            if created:
                user.set_password("password123")
                user.save()
            staff, created = MacraStaff.objects.get_or_create(
                user=user,
                defaults={
                    "phone_number": f"0999{i}0000",
                    "job_title": "Investigator",
                    "department": "Complaints",
                    "hire_date": timezone.now().date(),
                    "skills": inv["skills"],
                }
            )
            if created:
                self.stdout.write(self.style.SUCCESS(f"Created investigator: {user.username} with skills: {inv['skills']}"))
            else:
                self.stdout.write(f"Investigator {user.username} already exists.")
