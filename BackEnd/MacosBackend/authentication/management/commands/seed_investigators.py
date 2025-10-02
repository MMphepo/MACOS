from django.core.management.base import BaseCommand
from authentication.models import Users, MacraStaff
from django.utils import timezone

class Command(BaseCommand):
    help = 'Seed the database with 8 investigators and their skills.'

    def handle(self, *args, **kwargs):

        # Only create user accounts for MacraStaff investigators who do not have a user
        all_investigators = MacraStaff.objects.all()
        for staff in all_investigators:
            if not staff.user:
                username = f"investigator{staff.id}"
                user = Users.objects.create(
                    username=username,
                    first_name=getattr(staff, 'first_name', ''),
                    last_name=getattr(staff, 'last_name', ''),
                    role="macra_staff",
                    is_staff=True,
                    is_active=True,
                )
                user.set_password("password123")
                user.save()
                staff.user = user
                staff.save()
                self.stdout.write(self.style.SUCCESS(f"Created user account for existing investigator: {username}"))
            else:
                # Optionally, ensure password is set
                if not staff.user.has_usable_password():
                    staff.user.set_password("password123")
                    staff.user.save()
                    self.stdout.write(self.style.SUCCESS(f"Set password for investigator: {staff.user.username}"))

        # Now, for all existing MacraStaff investigators, ensure they have a user account
        all_investigators = MacraStaff.objects.all()
        for staff in all_investigators:
            if not staff.user:
                # Create a user for this staff
                username = f"investigator{staff.id}"
                user = Users.objects.create(
                    username=username,
                    first_name=staff.user.first_name if staff.user else "",
                    last_name=staff.user.last_name if staff.user else "",
                    role="macra_staff",
                    is_staff=True,
                    is_active=True,
                )
                user.set_password("password123")
                user.save()
                staff.user = user
                staff.save()
                self.stdout.write(self.style.SUCCESS(f"Created user account for existing investigator: {username}"))
            else:
                # Optionally, ensure password is set
                if not staff.user.has_usable_password():
                    staff.user.set_password("password123")
                    staff.user.save()
                    self.stdout.write(self.style.SUCCESS(f"Set password for investigator: {staff.user.username}"))
